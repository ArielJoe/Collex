import express from 'express';
import mongoose from 'mongoose';

// Import Models
import Registration from '../model/Registration.js';
import Attendance from '../model/Attendance.js';
// Other models can be imported as needed

const router = express.Router();

// --- GET All Active Tickets (Confirmed or Pending) for a specific User ---
router.get('/my-tickets/:userId', async (req, res) => {
    const { userId } = req.params;
    console.log(userId);

    if (!userId || !mongoose.Types.ObjectId.isValid(userId)) {
        return res.status(400).json({ success: false, message: 'A valid User ID must be provided in the URL.' });
    }

    try {
        const tickets = await Registration.aggregate([
            { $match: { user_id: new mongoose.Types.ObjectId(userId) } },
            { $lookup: { from: "payment", localField: "payment_id", foreignField: "_id", as: "paymentInfo" } },
            { $unwind: "$paymentInfo" },
            { $match: { "paymentInfo.status": { $in: ["confirmed", "pending", "rejected"] } } },
            { $lookup: { from: "event", localField: "event_id", foreignField: "_id", as: "eventInfo" } },
            { $lookup: { from: "event_detail", localField: "detail_id", foreignField: "_id", as: "detailInfo" } },
            { $lookup: { from: "event_package", localField: "package_id", foreignField: "_id", as: "packageInfo" } },
            { $unwind: "$eventInfo" },
            { $unwind: { path: "$detailInfo", preserveNullAndEmptyArrays: true } },
            { $unwind: { path: "$packageInfo", preserveNullAndEmptyArrays: true } },
            {
                $project: {
                    _id: 0,
                    eventId: "$eventInfo._id",
                    eventName: "$eventInfo.name",
                    location: "$eventInfo.location",
                    poster_url: "$eventInfo.poster_url",
                    start_time: "$eventInfo.start_time",
                    end_time: "$eventInfo.end_time",
                    purchasedItem: {
                        type: { $cond: { if: "$detailInfo", then: "Detail", else: "Package" } },
                        name: { $ifNull: ["$detailInfo.title", "$packageInfo.package_name"] },
                        price: { $ifNull: [{ $toDouble: "$detailInfo.price" }, { $toDouble: "$packageInfo.price" }] },
                    },
                    paymentStatus: "$paymentInfo.status",
                    confirmedAt: "$paymentInfo.confirmed_at",
                }
            },
            {
                $group: {
                    _id: "$eventId",
                    eventName: { $first: "$eventName" },
                    location: { $first: "$location" },
                    poster_url: { $first: "$poster_url" },
                    start_time: { $first: "$start_time" },
                    end_time: { $first: "$end_time" },
                    paymentStatus: { $first: "$paymentStatus" },
                    confirmedAt: { $first: "$confirmedAt" },
                    purchasedItems: {
                        $push: {
                            type: "$purchasedItem.type",
                            name: "$purchasedItem.name",
                            price: "$purchasedItem.price",
                        }
                    }
                }
            },
            {
                $project: {
                    _id: 0,
                    eventId: "$_id",
                    eventName: 1,
                    location: 1,
                    poster_url: 1,
                    start_time: 1,
                    end_time: 1,
                    paymentStatus: 1,
                    confirmedAt: 1,
                    purchasedItems: 1
                }
            }
        ]);

        console.log(tickets);
        res.status(200).json({
            success: true,
            message: tickets.length > 0 ? 'Active tickets fetched successfully.' : 'No active tickets found.',
            data: tickets
        });

    } catch (error) {
        console.error('Error fetching member tickets:', error);
        res.status(500).json({ success: false, message: 'Server error while fetching tickets.', error: error.message });
    }
});

// The rest of your routes remain the same...
router.get('/:id', async (req, res) => {
    const { _id: registrationId } = req.params;
    const { user_id: userId } = req.query;

    if (!registrationId || !mongoose.Types.ObjectId.isValid(registrationId)) {
        return res.status(400).json({ success: false, message: 'A valid Registration ID must be provided in the URL.' });
    }
    if (!userId || !mongoose.Types.ObjectId.isValid(userId)) {
        return res.status(400).json({ success: false, message: 'A valid user_id must be provided as a query parameter.' });
    }

    try {
        const registration = await Registration.findById(registrationId)
            .populate({ path: 'event_id', select: 'name location start_time end_time' })
            .populate({ path: 'detail_id', select: 'title' })
            .populate({ path: 'package_id', select: 'package_name' })
            .populate({ path: 'payment_id', select: 'status confirmed_at' });

        if (!registration) {
            return res.status(404).json({ success: false, message: 'Registration not found.' });
        }

        if (registration.user_id.toString() !== userId) {
            return res.status(403).json({ success: false, message: 'You are not authorized to view this registration.' });
        }

        const attendance = await Attendance.findOne({ registration_id: registration._id });

        res.status(200).json({
            success: true,
            message: 'Registration details fetched successfully.',
            data: {
                registrationId: registration._id,
                event: registration.event_id,
                item: registration.detail_id || registration.package_id,
                payment: registration.payment_id,
                qr_code: attendance ? attendance.qr_code : null,
                attendance_status: attendance ? (attendance.scanned_at ? 'scanned' : 'not_scanned') : 'not_generated',
                scanned_at: attendance ? attendance.scanned_at : null
            }
        });

    } catch (error) {
        console.error('Error fetching single registration detail:', error);
        res.status(500).json({ success: false, message: 'Server error while fetching registration detail.', error: error.message });
    }
});

export default router;