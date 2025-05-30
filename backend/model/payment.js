import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const PaymentSchema = Schema({
    registration_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Registration', required: true },
    proof_url: { type: String, required: true },
    amount: { type: mongoose.Schema.Types.Decimal128, required: true },
    status: {
        type: String,
        enum: ['pending', 'confirmed', 'rejected'],
        default: 'pending'
    },
    confirmed_by: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
    confirmed_at: { type: Date },
    created_at: { type: Date, default: Date.now }
}, {
    collection: "payment",
});

export default model('Payment', PaymentSchema);
