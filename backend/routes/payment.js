import { Router } from 'express';
import mongoose from 'mongoose';
import Registration from '../model/Registration.js'; // Path ke model Registration
import Payment from '../model/Payment.js';       // Path ke model Payment
import Event from '../model/Event.js';           // Untuk cek event
import User from '../model/User.js';             // Untuk cek user

const router = Router();

// Endpoint untuk user submit bukti pembayaran (setelah registrasi atau untuk registrasi yang sudah ada)
router.post('/submit-proof', async (req, res) => {
    const { user_id, event_id, detail_id, package_id, proof_url, amount } = req.body;

    if (!user_id || !event_id || !proof_url || !amount) {
        return res.status(400).json({ success: false, message: 'User ID, Event ID, proof URL, and amount are required' });
    }
    if (detail_id && package_id) {
        return res.status(400).json({ success: false, message: 'Cannot register for both a detail and a package simultaneously.' });
    }
    if (!detail_id && !package_id) {
        // Jika tidak ada detail_id atau package_id, bisa jadi ini registrasi umum ke event (jika didukung)
        // atau error. Untuk saat ini, asumsikan salah satu harus ada jika event punya detail/package.
        // Atau, jika registrasi hanya ke event, pastikan logicnya sesuai.
        // Untuk skema saat ini, detail_id atau package_id bersifat opsional di Registration.
        // Namun, payment biasanya terkait dengan sesuatu yang berbayar (detail/package).
        console.warn("Payment submission without specific detail_id or package_id.");
    }


    const session = await mongoose.startSession(); // Gunakan transaksi untuk konsistensi data
    session.startTransaction();

    try {
        // Validasi User dan Event
        const userExists = await User.findById(user_id).session(session);
        if (!userExists) {
            await session.abortTransaction();
            session.endSession();
            return res.status(404).json({ success: false, message: 'User not found.' });
        }
        const eventExists = await Event.findById(event_id).session(session);
        if (!eventExists) {
            await session.abortTransaction();
            session.endSession();
            return res.status(404).json({ success: false, message: 'Event not found.' });
        }
        if (new Date() > new Date(eventExists.registration_deadline)) {
            await session.abortTransaction();
            session.endSession();
            return res.status(400).json({ success: false, message: 'Registration deadline for this event has passed.' });
        }


        // Cari atau buat registrasi
        let registration = await Registration.findOne({
            user_id,
            event_id,
            // Jika registrasi bisa di-update untuk detail/package berbeda, logika ini perlu disesuaikan.
            // Untuk saat ini, asumsikan satu registrasi per user per event (bisa punya detail/package).
            // Jika ingin user bisa mendaftar ke banyak detail/package dalam satu event,
            // maka detail_id/package_id harus jadi bagian dari query pencarian registrasi.
        }).session(session);

        let isNewRegistration = false;
        if (!registration) {
            // Cek apakah event sudah mencapai max_participants jika registrasi baru
            const currentRegistrationsCount = await Registration.countDocuments({ event_id, payment_status: 'confirmed' }).session(session);
            if (currentRegistrationsCount >= eventExists.max_participants) {
                await session.abortTransaction();
                session.endSession();
                return res.status(400).json({ success: false, message: 'Event has reached its maximum number of participants.' });
            }

            registration = new Registration({
                user_id,
                event_id,
                detail_id: detail_id || null,
                package_id: package_id || null,
                payment_status: 'pending', // Status awal saat submit bukti
                // registration_date akan default dari schema
            });
            await registration.save({ session });
            isNewRegistration = true;
        } else {
            // Jika registrasi sudah ada dan sudah 'confirmed', mungkin tidak boleh submit bukti lagi
            if (registration.payment_status === 'confirmed') {
                await session.abortTransaction();
                session.endSession();
                return res.status(400).json({ success: false, message: 'This registration is already confirmed. Cannot submit new payment proof.' });
            }
            // Update status jika registrasi sudah ada tapi belum 'pending' (misal 'rejected' sebelumnya)
            registration.payment_status = 'pending';
            if (detail_id) registration.detail_id = detail_id; // Izinkan update detail/package jika registrasi ada
            if (package_id) registration.package_id = package_id;
            await registration.save({ session });
        }

        // Buat atau update record Payment
        let payment = await Payment.findOne({ registration_id: registration._id }).session(session);
        if (payment) {
            payment.proof_url = proof_url;
            payment.amount = mongoose.Types.Decimal128.fromString(amount.toString());
            payment.status = 'pending'; // Selalu pending saat submit/resubmit bukti
            // payment.created_at tidak diupdate, confirmed_by & confirmed_at direset
            payment.confirmed_by = null;
            payment.confirmed_at = null;
            await payment.save({ session });
        } else {
            payment = new Payment({
                registration_id: registration._id,
                proof_url,
                amount: mongoose.Types.Decimal128.fromString(amount.toString()),
                status: 'pending',
                // created_at akan default dari schema
            });
            await payment.save({ session });
        }

        await session.commitTransaction();
        session.endSession();

        res.status(201).json({
            success: true,
            message: `Payment proof submitted successfully. Your registration is ${isNewRegistration ? 'created and ' : ''}now pending confirmation.`,
            registration_id: registration._id,
            payment_id: payment._id,
            payment_status: registration.payment_status
        });

    } catch (error) {
        await session.abortTransaction();
        session.endSession();
        console.error('Error submitting payment proof:', error);
        res.status(500).json({ success: false, message: 'Server error', error: error.message });
    }
});


// Route untuk admin/finance mengkonfirmasi atau menolak pembayaran
router.patch('/update-payment-status/:paymentId', async (req, res) => {
    // Perlu middleware otentikasi dan otorisasi (misal, hanya admin/finance)
    const { paymentId } = req.params;
    const { status, confirmed_by_user_id } = req.body; // status: 'confirmed' atau 'rejected'

    if (!status || !['confirmed', 'rejected'].includes(status)) {
        return res.status(400).json({ success: false, message: 'Invalid status. Must be "confirmed" or "rejected".' });
    }
    if (status === 'confirmed' && !confirmed_by_user_id) {
        return res.status(400).json({ success: false, message: 'Confirmed by user ID is required for confirming payment.' });
    }

    const session = await mongoose.startSession();
    session.startTransaction();

    try {
        const payment = await Payment.findById(paymentId).session(session);
        if (!payment) {
            await session.abortTransaction();
            session.endSession();
            return res.status(404).json({ success: false, message: 'Payment record not found.' });
        }

        const registration = await Registration.findById(payment.registration_id).session(session);
        if (!registration) {
            await session.abortTransaction();
            session.endSession();
            return res.status(404).json({ success: false, message: 'Associated registration not found.' });
        }

        // Update payment
        payment.status = status;
        if (status === 'confirmed') {
            payment.confirmed_by = confirmed_by_user_id;
            payment.confirmed_at = new Date();
        } else { // rejected
            payment.confirmed_by = null;
            payment.confirmed_at = null;
        }
        await payment.save({ session });

        // Update registration
        registration.payment_status = status;
        await registration.save({ session });

        await session.commitTransaction();
        session.endSession();

        res.status(200).json({
            success: true,
            message: `Payment status updated to ${status}.`,
            payment,
            registration
        });

    } catch (error) {
        await session.abortTransaction();
        session.endSession();
        console.error('Error updating payment status:', error);
        res.status(500).json({ success: false, message: 'Server error', error: error.message });
    }
});

// GET payment status untuk user (berdasarkan user_id dan event_id)
router.get('/status', async (req, res) => {
    const { user_id, event_id } = req.query;

    if (!user_id || !event_id) {
        return res.status(400).json({ success: false, message: 'User ID and Event ID are required query parameters.' });
    }

    try {
        const registration = await Registration.findOne({ user_id, event_id })
            .populate('detail_id', 'session_title price')
            .populate('package_id', 'package_name price');

        if (!registration) {
            return res.status(200).json({ // Mengembalikan 200 agar frontend bisa handle "not registered"
                success: true,
                registered: false,
                message: 'No registration found for this user and event.',
                payment_status: 'not_registered'
            });
        }

        const payment = await Payment.findOne({ registration_id: registration._id });

        res.status(200).json({
            success: true,
            registered: true,
            registration_id: registration._id,
            payment_status: registration.payment_status,
            registration_date: registration.registration_date,
            detail: registration.detail_id,
            package: registration.package_id,
            payment_details: payment ? {
                payment_id: payment._id,
                proof_url: payment.proof_url,
                amount: payment.amount.toString(),
                payment_record_status: payment.status,
                confirmed_at: payment.confirmed_at,
                payment_created_at: payment.created_at
            } : null
        });

    } catch (error) {
        console.error('Error checking payment status:', error);
        res.status(500).json({ success: false, message: 'Server error while checking payment status' });
    }
});

// GET semua payments (untuk admin/finance, dengan filter dan pagination)
router.get('/all', async (req, res) => {
    // Perlu middleware otentikasi dan otorisasi
    const { page = 1, limit = 10, status, event_id } = req.query;
    let filter = {};
    if (status && ['pending', 'confirmed', 'rejected'].includes(status)) {
        filter.status = status;
    }

    let registrationFilter = {};
    if (event_id) {
        registrationFilter.event_id = event_id;
    }

    try {
        let paymentQuery = Payment.find(filter);

        if (event_id) {
            // Jika ada filter event_id, kita perlu mencari registrasi dulu, lalu payment
            const registrations = await Registration.find(registrationFilter).select('_id');
            const registrationIds = registrations.map(r => r._id);
            paymentQuery = Payment.find({ ...filter, registration_id: { $in: registrationIds } });
        }

        const payments = await paymentQuery
            .populate({
                path: 'registration_id',
                select: 'user_id event_id payment_status',
                populate: [
                    { path: 'user_id', select: 'full_name email' },
                    { path: 'event_id', select: 'name' }
                ]
            })
            .sort({ created_at: -1 })
            .limit(parseInt(limit))
            .skip((parseInt(page) - 1) * parseInt(limit))
            .exec();

        // Untuk total count yang akurat dengan filter event_id
        let totalCountQuery = Payment.countDocuments(filter);
        if (event_id) {
            const registrations = await Registration.find(registrationFilter).select('_id');
            const registrationIds = registrations.map(r => r._id);
            totalCountQuery = Payment.countDocuments({ ...filter, registration_id: { $in: registrationIds } });
        }
        const totalCount = await totalCountQuery;


        res.status(200).json({
            success: true,
            data: payments,
            totalPages: Math.ceil(totalCount / parseInt(limit)),
            currentPage: parseInt(page),
            totalPayments: totalCount
        });

    } catch (error) {
        console.error('Error fetching all payments:', error);
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

export default router;
