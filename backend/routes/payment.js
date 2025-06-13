import express from 'express';
import mongoose from 'mongoose';

// Impor Model
import Cart from '../model/Cart.js';
import Event from '../model/Event.js';
import EventDetail from '../model/EventDetail.js';
import EventPackage from '../model/EventPackage.js';
import User from '../model/User.js';
import Registration from '../model/Registration.js';
import Payment from '../model/Payment.js';
import Attendance from '../model/Attendance.js';

const router = express.Router();

// Endpoint untuk user submit bukti pembayaran untuk satu registrasi
router.post('/submit-proof', async (req, res) => {
    const { user_id, event_id, detail_id, package_id, proof_url, amount } = req.body;

    if (!user_id || !event_id || !proof_url || !amount) {
        return res.status(400).json({ success: false, message: 'User ID, Event ID, proof URL, and amount are required' });
    }
    if (!mongoose.Types.ObjectId.isValid(user_id) || !mongoose.Types.ObjectId.isValid(event_id) || (detail_id && !mongoose.Types.ObjectId.isValid(detail_id)) || (package_id && !mongoose.Types.ObjectId.isValid(package_id))) {
        return res.status(400).json({ success: false, message: 'Invalid ID format provided.' });
    }

    try {
        const userExists = await User.findById(user_id);
        if (!userExists) return res.status(404).json({ success: false, message: 'User not found.' });

        const eventExists = await Event.findById(event_id);
        if (!eventExists) return res.status(404).json({ success: false, message: 'Event not found.' });

        if (new Date() > new Date(eventExists.registration_deadline)) {
            return res.status(400).json({ success: false, message: 'Registration deadline for this event has passed.' });
        }

        let registration = await Registration.findOne({ user_id, event_id, detail_id: detail_id || null, package_id: package_id || null }).populate('payment_id');
        let isNewRegistration = false;
        let paymentDocument;

        if (!registration || (registration.payment_id && registration.payment_id.status === 'rejected')) {
            if (registration && registration.payment_id && registration.payment_id.status === 'rejected') {
                // Jika payment lama ditolak, kita akan membuat payment baru dan menautkannya.
                // Pertimbangkan apakah payment lama perlu dihapus atau hanya ditandai. Untuk saat ini, kita buat baru.
                console.log(`Registration ${registration._id} found with rejected payment. Creating new payment.`);
            }

            paymentDocument = new Payment({
                user_id: user_id,
                proof_url,
                amount: mongoose.Types.Decimal128.fromString(amount.toString()),
                status: 'pending',
                // registration_id akan diisi setelah registration dibuat/diupdate
            });
            await paymentDocument.save();

            if (!registration) {
                const registrationsForEvent = await Registration.find({ event_id: eventExists._id }).populate('payment_id');
                const actualConfirmedCount = registrationsForEvent.filter(r => r.payment_id && r.payment_id.status === 'confirmed').length;

                if (actualConfirmedCount >= eventExists.max_participants) {
                    await Payment.findByIdAndDelete(paymentDocument._id);
                    return res.status(400).json({ success: false, message: 'Event has reached its maximum number of participants.' });
                }
                registration = new Registration({
                    user_id,
                    event_id,
                    detail_id: detail_id || null,
                    package_id: package_id || null,
                    payment_id: paymentDocument._id, // Tautkan ke payment baru
                });
                isNewRegistration = true;
            } else {
                registration.payment_id = paymentDocument._id; // Update payment_id pada registrasi yang sudah ada
            }
            await registration.save();

            paymentDocument.registration_id = registration._id; // Tautkan payment ke registration ini
            await paymentDocument.save();

        } else if (registration.payment_id && registration.payment_id.status === 'confirmed') {
            return res.status(400).json({ success: false, message: 'This registration is already confirmed.' });
        } else if (registration.payment_id && registration.payment_id.status === 'pending') {
            // Jika sudah ada payment pending, update bukti dan amountnya
            paymentDocument = registration.payment_id;
            paymentDocument.proof_url = proof_url;
            paymentDocument.amount = mongoose.Types.Decimal128.fromString(amount.toString());
            // status tetap pending, tidak perlu diubah
            await paymentDocument.save();
        } else {
            return res.status(500).json({ success: false, message: 'Inconsistent registration or payment state.' });
        }

        res.status(201).json({
            success: true,
            message: `Payment proof submitted successfully. Your registration is ${isNewRegistration ? 'created and ' : ''}now pending confirmation.`,
            registration_id: registration._id,
            payment_id: paymentDocument._id,
        });

    } catch (error) {
        console.error('Error submitting single payment proof:', error);
        res.status(500).json({ success: false, message: 'Server error during payment submission.', error: error.message });
    }
});


// Endpoint untuk memproses checkout seluruh keranjang
router.post('/process-cart-checkout', async (req, res) => {
    const { user_id, total_amount, proof_url, cart_items } = req.body;

    if (!user_id || !total_amount || !proof_url || !Array.isArray(cart_items) || cart_items.length === 0) {
        return res.status(400).json({ success: false, message: 'User ID, total amount, proof URL, and cart items are required.' });
    }
    if (!mongoose.Types.ObjectId.isValid(user_id)) {
        return res.status(400).json({ success: false, message: 'Invalid User ID format.' });
    }

    try {
        const user = await User.findById(user_id);
        if (!user) {
            return res.status(404).json({ success: false, message: 'User not found.' });
        }

        const validRegistrationPayloads = [];

        for (const cartItem of cart_items) {
            if (!cartItem.event_id || !cartItem.item_id || !cartItem.item_type) {
                throw new Error('Invalid cart item structure: missing event_id, item_id, or item_type.');
            }
            if (!mongoose.Types.ObjectId.isValid(cartItem.event_id) || !mongoose.Types.ObjectId.isValid(cartItem.item_id)) {
                throw new Error(`Invalid ID format in cart item: ${JSON.stringify(cartItem)}`);
            }

            const event = await Event.findById(cartItem.event_id);
            if (!event) throw new Error(`Event with ID ${cartItem.event_id} not found for cart item "${cartItem.item_id}".`);
            if (new Date() > new Date(event.registration_deadline)) throw new Error(`Registration deadline for event "${event.name}" has passed.`);

            const registrationsForEvent = await Registration.find({ event_id: event._id }).populate('payment_id');
            const actualConfirmedCount = registrationsForEvent.filter(r => r.payment_id && r.payment_id.status === 'confirmed').length;

            if (actualConfirmedCount >= event.max_participants) {
                // Jika kuota penuh, lewati item ini dan jangan tambahkan ke validRegistrationPayloads
                console.warn(`Event "${event.name}" has reached its maximum participants. Skipping item ${cartItem.item_id}.`);
                continue;
            }

            const existingRegistrationQuery = { user_id, event_id: cartItem.event_id };
            if (cartItem.item_type === 'detail') existingRegistrationQuery.detail_id = cartItem.item_id;
            else if (cartItem.item_type === 'package') existingRegistrationQuery.package_id = cartItem.item_id;

            const existingRegistration = await Registration.findOne(existingRegistrationQuery).populate('payment_id');
            if (existingRegistration && existingRegistration.payment_id &&
                (existingRegistration.payment_id.status === 'confirmed' || existingRegistration.payment_id.status === 'pending')) {
                console.warn(`User already has a ${existingRegistration.payment_id.status} registration for item ${cartItem.item_id} in event ${cartItem.event_id}. Skipping.`);
                continue;
            }

            validRegistrationPayloads.push({
                user_id,
                event_id: cartItem.event_id,
                detail_id: cartItem.item_type === 'detail' ? cartItem.item_id : null,
                package_id: cartItem.item_type === 'package' ? cartItem.item_id : null,
            });
        }

        if (validRegistrationPayloads.length === 0) {
            return res.status(400).json({ success: false, message: 'Tidak ada item baru untuk diregistrasi. Mungkin semua item sudah terdaftar, menunggu pembayaran, atau kuota event penuh.' });
        }

        // 1. Buat Payment document instance (belum disimpan, _id sudah ada)
        const payment = new Payment({
            user_id: user_id,
            amount: mongoose.Types.Decimal128.fromString(total_amount.toString()),
            proof_url: proof_url,
            status: 'pending',
            // registration_id akan diisi setelah registrasi pertama berhasil disimpan
        });
        // Mongoose akan otomatis membuat _id saat instance dibuat

        // 2. Buat semua Registration document instances, tautkan dengan payment._id
        const registrationDocuments = validRegistrationPayloads.map(regData =>
            new Registration({
                ...regData,
                payment_id: payment._id
            })
        );

        // 3. Simpan semua Registration documents
        const finalRegistrations = await Registration.insertMany(registrationDocuments);

        if (!finalRegistrations || finalRegistrations.length === 0) {
            throw new Error("Tidak ada registrasi yang berhasil dibuat setelah proses validasi.");
        }

        // 4. Update Payment dengan registration_id dari registrasi pertama dan simpan Payment
        payment.registration_id = finalRegistrations[0]._id;
        await payment.save();

        // 5. Kosongkan keranjang pengguna
        await Cart.deleteMany({ user_id: user_id });

        res.status(201).json({
            success: true,
            message: 'Pembayaran Anda berhasil dikirim dan sedang menunggu konfirmasi. Keranjang Anda telah dikosongkan.',
            payment_id: payment._id,
            registrations: finalRegistrations.map(r => r._id)
        });

    } catch (error) {
        console.error('Error processing cart checkout:', error);
        res.status(500).json({ success: false, message: error.message || 'Terjadi kesalahan pada server saat memproses checkout.' });
    }
});


// Route untuk admin/finance mengkonfirmasi atau menolak pembayaran
router.patch('/update-payment-status/:paymentId', async (req, res) => {
    const { paymentId } = req.params;
    const { status, confirmed_by_user_id } = req.body;

    if (!status || !['confirmed', 'rejected'].includes(status)) {
        return res.status(400).json({ success: false, message: 'Invalid status. Must be "confirmed" or "rejected".' });
    }
    if (status === 'confirmed' && !confirmed_by_user_id) {
        return res.status(400).json({ success: false, message: 'Confirmed by user ID is required for confirming payment.' });
    }
    if (confirmed_by_user_id && !mongoose.Types.ObjectId.isValid(confirmed_by_user_id)) {
        return res.status(400).json({ success: false, message: 'Invalid Confirmed By User ID format.' });
    }
    if (!mongoose.Types.ObjectId.isValid(paymentId)) {
        return res.status(400).json({ success: false, message: 'Invalid Payment ID format.' });
    }

    try {
        const payment = await Payment.findById(paymentId);
        if (!payment) {
            return res.status(404).json({ success: false, message: 'Payment record not found.' });
        }

        const oldStatus = payment.status;
        payment.status = status;
        if (status === 'confirmed') {
            payment.confirmed_by = confirmed_by_user_id;
            payment.confirmed_at = new Date();
        } else {
            payment.confirmed_by = null;
            payment.confirmed_at = null;
        }
        await payment.save();

        const linkedRegistrations = await Registration.find({ payment_id: payment._id }).populate('event_id');

        if (status === 'confirmed' && oldStatus !== 'confirmed') {
            for (const registration of linkedRegistrations) {
                if (registration.event_id && typeof registration.event_id.max_participants === 'number') {
                    await Event.findByIdAndUpdate(registration.event_id._id, {
                        $inc: { registered_participant: 1 }
                    });
                    console.log(`Incremented participant count for event ${registration.event_id._id}`);
                }

                if (registration.detail_id) {
                    const existingAttendance = await Attendance.findOne({
                        registration_id: registration._id,
                        detail_id: registration.detail_id
                    });

                    if (!existingAttendance) {
                        const eventDetail = await EventDetail.findById(registration.detail_id);
                        if (!eventDetail) {
                            console.warn(`EventDetail not found for ID ${registration.detail_id} during attendance generation.`);
                            continue;
                        }

                        const newAttendance = new Attendance({
                            registration_id: registration._id,
                            detail_id: registration.detail_id,
                            qr_code: new mongoose.Types.ObjectId().toString(),
                            scanned_by: null,
                            scanned_at: null,
                        });
                        await newAttendance.save();
                        console.log(`Attendance generated for registration ${registration._id} and detail ${registration.detail_id}`);
                    } else {
                        console.log(`Attendance already exists for registration ${registration._id} and detail ${registration.detail_id}`);
                    }
                }
            }
        } else if (oldStatus === 'confirmed' && status !== 'confirmed') {
            for (const registration of linkedRegistrations) {
                if (registration.event_id && typeof registration.event_id.max_participants === 'number') {
                    await Event.findByIdAndUpdate(registration.event_id._id, {
                        $inc: { registered_participant: -1 }
                    });
                    console.log(`Decremented participant count for event ${registration.event_id._id}`);
                }
                if (registration.detail_id) {
                    await Attendance.deleteOne({ registration_id: registration._id, detail_id: registration.detail_id });
                    console.log(`Attendance deleted for registration ${registration._id} and detail ${registration.detail_id} due to payment status change from confirmed.`);
                }
            }
        }

        res.status(200).json({
            success: true,
            message: `Payment status updated to ${status}. Attendance and participant count handled if applicable.`,
            payment
        });

    } catch (error) {
        console.error('Error updating payment status:', error);
        res.status(500).json({ success: false, message: 'Server error while updating payment status.', error: error.message });
    }
});

// GET payment status untuk user (berdasarkan user_id dan event_id)
router.get('/status', async (req, res) => {
    const { user_id, event_id, detail_id, package_id } = req.query;

    if (!user_id || !event_id) {
        return res.status(400).json({ success: false, message: 'User ID and Event ID are required query parameters.' });
    }
    if (!mongoose.Types.ObjectId.isValid(user_id) || !mongoose.Types.ObjectId.isValid(event_id) || (detail_id && !mongoose.Types.ObjectId.isValid(detail_id)) || (package_id && !mongoose.Types.ObjectId.isValid(package_id))) {
        return res.status(400).json({ success: false, message: 'Invalid ID format provided.' });
    }

    try {
        const query = { user_id, event_id };
        if (detail_id) query.detail_id = detail_id;
        if (package_id) query.package_id = package_id;

        const registration = await Registration.findOne(query)
            .sort({ registration_date: -1 })
            .populate({ path: 'detail_id', select: 'title price' })
            .populate({ path: 'package_id', select: 'package_name price' })
            .populate({ path: 'payment_id', select: 'status proof_url amount confirmed_at created_at' });

        if (!registration) {
            return res.status(200).json({
                success: true,
                registered: false,
                message: 'No registration found for this user, event, and specific item.',
                payment_status: 'not_registered'
            });
        }

        const paymentStatus = registration.payment_id ? registration.payment_id.status : 'not_paid';

        res.status(200).json({
            success: true,
            registered: true,
            registration_id: registration._id,
            payment_status: paymentStatus,
            registration_date: registration.registration_date,
            detail: registration.detail_id,
            package: registration.package_id,
            payment_details: registration.payment_id ? {
                payment_id: registration.payment_id._id,
                proof_url: registration.payment_id.proof_url,
                amount: registration.payment_id.amount.toString(),
                payment_record_status: registration.payment_id.status,
                confirmed_at: registration.payment_id.confirmed_at,
                payment_created_at: registration.payment_id.created_at
            } : null,
        });

    } catch (error) {
        console.error('Error checking payment status:', error);
        res.status(500).json({ success: false, message: 'Server error while checking payment status' });
    }
});

// GET semua payments (untuk admin/finance, dengan filter dan pagination)
router.get('/all', async (req, res) => {
    const { page = 1, limit = 10, status, event_id, user_search } = req.query;
    let filter = {};
    if (status && ['pending', 'confirmed', 'rejected'].includes(status)) {
        filter.status = status;
    }

    let registrationConditions = [];
    if (event_id) {
        if (!mongoose.Types.ObjectId.isValid(event_id)) return res.status(400).json({ success: false, message: "Invalid event_id format" });
        registrationConditions.push({ event_id: event_id });
    }
    if (user_search) {
        const users = await User.find({
            $or: [
                { full_name: { $regex: user_search, $options: 'i' } },
                { email: { $regex: user_search, $options: 'i' } }
            ]
        }).select('_id');
        if (users.length > 0) {
            registrationConditions.push({ user_id: { $in: users.map(u => u._id) } });
        } else {
            return res.status(200).json({ success: true, data: [], totalPages: 0, currentPage: 1, totalPayments: 0 });
        }
    }

    try {
        let paymentQuery = Payment.find(filter);

        if (registrationConditions.length > 0) {
            const registrationFilter = registrationConditions.length > 1 ? { $and: registrationConditions } : registrationConditions[0];
            const registrations = await Registration.find(registrationFilter).select('_id payment_id');
            const paymentIdsFromRegistrations = [...new Set(registrations.map(r => r.payment_id).filter(id => id))];
            if (paymentIdsFromRegistrations.length === 0 && (event_id || user_search)) {
                return res.status(200).json({ success: true, data: [], totalPages: 0, currentPage: 1, totalPayments: 0 });
            }
            paymentQuery = paymentQuery.where('_id').in(paymentIdsFromRegistrations);
        }

        const totalPayments = await Payment.countDocuments(paymentQuery.getFilter());

        const payments = await paymentQuery
            .populate({
                path: 'user_id',
                select: 'full_name email'
            })
            .populate('confirmed_by', 'full_name email')
            .sort({ created_at: -1 })
            .limit(parseInt(limit))
            .skip((parseInt(page) - 1) * parseInt(limit))
            .exec();

        const paymentsWithAllRegistrations = await Promise.all(payments.map(async (payment) => {
            const allLinkedRegistrations = await Registration.find({ payment_id: payment._id })
                .populate({
                    path: 'event_id',
                    select: 'name' // Ensure event name is included
                })
                .populate({
                    path: 'detail_id',
                    select: 'title price' // Ensure detail title and price are included
                })
                .populate({
                    path: 'package_id',
                    select: 'package_name price' // Ensure package name and price are included
                })
                .populate({
                    path: 'user_id',
                    select: 'full_name email'
                });
            return { ...payment.toObject(), all_registrations: allLinkedRegistrations };
        }));

        res.status(200).json({
            success: true,
            data: paymentsWithAllRegistrations,
            totalPages: Math.ceil(totalPayments / parseInt(limit)),
            currentPage: parseInt(page),
            totalPayments: totalPayments
        });
    } catch (error) {
        console.error('Error fetching all payments:', error);
        res.status(500).json({ success: false, message: 'Server error while fetching payments' });
    }
});

export default router;