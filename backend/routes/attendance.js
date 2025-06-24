import express from "express";
import Attendance from '../model/Attendance.js';
import Registration from '../model/Registration.js';
import EventDetail from '../model/EventDetail.js';
import User from '../model/User.js';

const router = express.Router();

// POST /api/attendance/scan
router.post('/scan', async (req, res) => {
    console.log('-------------------');
    console.log('QR SCAN REQUEST RECEIVED');
    console.log('Headers:', req.headers);
    console.log('Body:', req.body);

    const { qr_code, registration_id, event_detail_id } = req.body;
    const scanned_by = req.user?._id; // Make optional chaining in case user isn't populated

    try {
        // Validate input with detailed logging
        if (!qr_code) {
            console.error('Missing QR code in request');
            return res.status(400).json({
                success: false,
                message: 'QR code is required.'
            });
        }

        if (!registration_id) {
            console.error('Missing registration_id in request');
            return res.status(400).json({
                success: false,
                message: 'Registration ID is required.'
            });
        }

        if (!event_detail_id) {
            console.error('Missing event_detail_id in request');
            return res.status(400).json({
                success: false,
                message: 'Event detail ID is required.'
            });
        }

        console.log('Looking up attendance record for QR:', qr_code);

        // Find attendance record by QR code with detailed population
        const attendance = await Attendance.findOne({ qr_code })
            .populate({
                path: 'registration_id',
                populate: [
                    { path: 'user_id', select: 'full_name' },
                    { path: 'event_id', select: 'name organizer' },
                    { path: 'detail_id', select: 'title' }
                ]
            });

        if (!attendance) {
            console.error('No attendance record found for QR code:', qr_code);
            return res.status(404).json({
                success: false,
                message: 'Invalid QR code.'
            });
        }

        console.log('Found attendance record:', {
            id: attendance._id,
            registration: attendance.registration_id?._id,
            event: attendance.registration_id?.event_id?._id
        });

        // Verify organizer's permission
        const registration = attendance.registration_id;
        const event = registration.event_id;

        /*
        if (!event.organizer.equals(scanned_by)) {
            console.error('Unauthorized scan attempt:', {
                scanner: scanned_by,
                organizer: event.organizer
            });
            return res.status(403).json({
                success: false,
                message: 'You are not authorized to scan for this event.'
            });
        } */

        console.log('Updating attendance record with scan details');

        // Update attendance record
        attendance.scanned_by = scanned_by;
        attendance.scanned_at = new Date();
        await attendance.save();

        console.log('Attendance record updated successfully');

        // Prepare response data with event name
        const responseData = {
            event_name: event.name,
            user: { full_name: registration.user_id.full_name },
            event_detail: { title: registration.detail_id ? registration.detail_id.title : null }
        };

        console.log('Sending successful response:', responseData);

        res.status(200).json({
            success: true,
            data: responseData,
            message: 'Attendance confirmed.'
        });

    } catch (error) {
        console.error('ERROR PROCESSING QR SCAN:', {
            error: error.message,
            stack: error.stack,
            body: req.body,
            user: req.user
        });
        res.status(500).json({
            success: false,
            message: 'Server error while confirming attendance.'
        });
    } finally {
        console.log('-------------------');
    }
});

export default router;
