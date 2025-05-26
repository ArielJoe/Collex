import { Router } from 'express';
import Registration from '../model/Registration.js';
import Payment from '../model/Payment.js';

const router = Router();

router.post('/api/register-and-pay', async (req, res) => {
    try {
        const { user_id, event_id, proof_url, amount } = req.body;

        // Create registration
        const registration = new Registration({
            _id: new mongoose.Types.ObjectId().toString(),
            registration_id: new mongoose.Types.ObjectId().toString(),
            proof_url,
            amount,
            status: 'pending',
            created_at: new Date(),
            updated_at: new Date(),
        });
        await registration.save();

        // Create payment
        const payment = new Payment({
            _id: new mongoose.Types.ObjectId().toString(),
            user_id,
            event_id,
            qr_code: `aa580a69-3afe-41b0-b4c5-ce2ac2256518`, // Generate or fetch a QR code
            payment_status: 'pending',
            created_at: new Date(),
            updated_at: new Date(),
        });
        await payment.save();

        res.status(201).json({
            message: 'Registration and payment initiated successfully',
            registration,
            payment,
        });
    } catch (error) {
        res.status(500).json({ message: 'Server error', error });
    }
});

export default router;
