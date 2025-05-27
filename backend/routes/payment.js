import { Router } from 'express';
import mongoose from 'mongoose';
import Registration from '../model/Registration.js';
import Payment from '../model/Payment.js';

const router = Router();

router.post('/register-and-pay', async (req, res) => {
    try {
        const { user_id, event_id, proof_url, amount } = req.body;

        if (!user_id || !event_id || !proof_url || !amount) {
            return res.status(400).json({ message: 'Missing required fields' });
        }

        // Check if registration already exists
        let registration = await Registration.findOne({ user_id, event_id });

        if (registration) {
            // Update existing registration with proof submission
            registration.proof_submitted = true;
            registration.payment_status = 'pending';
            registration.updated_at = new Date();
            await registration.save();

            // Update or create payment record
            let payment = await Payment.findOne({ registration_id: registration._id });
            if (payment) {
                payment.proof_url = proof_url;
                payment.status = 'pending';
                payment.updated_at = new Date();
                await payment.save();
            } else {
                payment = new Payment({
                    registration_id: registration._id,
                    proof_url,
                    amount: mongoose.Types.Decimal128.fromString(amount.toString()),
                    status: 'pending',
                });
                await payment.save();
            }
        } else {
            // Create new registration
            registration = new Registration({
                user_id,
                event_id,
                qr_code: new mongoose.Types.ObjectId().toString(),
                payment_status: 'pending',
                proof_submitted: true,
                created_at: new Date(),
                updated_at: new Date(),
            });
            await registration.save();
            console.log('Registration saved:', registration);

            // Create payment
            const payment = new Payment({
                registration_id: registration._id,
                proof_url,
                amount: mongoose.Types.Decimal128.fromString(amount.toString()),
                status: 'pending',
                created_at: new Date(),
                updated_at: new Date(),
            });
            await payment.save();
            console.log('Payment saved:', payment);
        }

        res.status(201).json({
            message: 'Registration and payment proof submitted successfully',
            registration,
            proof_submitted: true,
        });
    } catch (error) {
        console.error('Server error:', error.message);
        res.status(500).json({ message: 'Server error', error: error.message });
    }
});

// Route to check payment status - REMOVED DUPLICATE
router.get('/check-payment-status', async (req, res) => {
    try {
        const { user_id, event_id } = req.query;

        console.log('Checking payment status for:', { user_id, event_id });

        // Validate input
        if (!user_id || !event_id) {
            return res.status(400).json({
                message: 'Missing user_id or event_id',
                proof_submitted: false
            });
        }

        // Check if a registration exists for this user and event
        const registration = await Registration.findOne({ user_id, event_id });

        console.log('Registration found:', registration);

        if (!registration) {
            return res.status(200).json({
                message: 'No registration found for this user and event',
                proof_submitted: false,
                registered: false,
                payment_status: 'not_found'
            });
        }

        // Check if there's a payment record
        const payment = await Payment.findOne({ registration_id: registration._id });

        console.log('Payment found:', payment);

        // Determine if proof has been submitted
        const proofSubmitted = registration.proof_submitted ||
            registration.payment_status === 'pending' ||
            (payment && payment.status === 'pending');

        // Return comprehensive payment status
        res.status(200).json({
            message: 'Payment status retrieved successfully',
            proof_submitted: proofSubmitted,
            registered: true,
            payment_status: registration.payment_status || 'pending',
            submitted_at: proofSubmitted ? (registration.updated_at || registration.created_at) : null,
            registration_id: registration._id,
            payment_proof_url: payment ? payment.proof_url : null
        });
    } catch (error) {
        console.error('Error checking payment status:', error);
        res.status(500).json({
            message: 'Server error while checking payment status',
            proof_submitted: false
        });
    }
});

// Optional: Route to get all registrations for a user
router.get('/user-registrations/:user_id', async (req, res) => {
    try {
        const { user_id } = req.params;

        const registrations = await Registration.find({ user_id }).populate('event_id');

        res.status(200).json({
            message: 'User registrations retrieved successfully',
            registrations
        });
    } catch (error) {
        console.error('Error getting user registrations:', error);
        res.status(500).json({ message: 'Server error while getting registrations' });
    }
});

// Optional: Route to update payment status (for admin use)
router.patch('/update-payment-status', async (req, res) => {
    try {
        const { registration_id, status } = req.body;

        if (!registration_id || !status) {
            return res.status(400).json({ message: 'Missing registration_id or status' });
        }

        const registration = await Registration.findByIdAndUpdate(
            registration_id,
            {
                payment_status: status,
                proof_submitted: status === 'pending',
                updated_at: new Date()
            },
            { new: true }
        );

        if (!registration) {
            return res.status(404).json({ message: 'Registration not found' });
        }

        // Also update payment record if exists
        await Payment.findOneAndUpdate(
            { registration_id },
            {
                status: status,
                updated_at: new Date()
            }
        );

        res.status(200).json({
            message: 'Payment status updated successfully',
            registration
        });
    } catch (error) {
        console.error('Error updating payment status:', error);
        res.status(500).json({ message: 'Server error while updating payment status' });
    }
});

export default router;
