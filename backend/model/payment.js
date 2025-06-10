import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const PaymentSchema = Schema({
    proof_url: { type: String, required: true },
    amount: { type: mongoose.Schema.Types.Decimal128, required: true },
    status: {
        type: String,
        enum: ['pending', 'confirmed', 'rejected'],
        default: 'pending'
    },
    confirmed_by: { type: mongoose.Schema.Types.ObjectId, ref: 'User' },
    confirmed_at: { type: Date },
    created_at: { type: Date, default: Date.now },
    user_id: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true }
}, {
    collection: "payment",
});

export default model('Payment', PaymentSchema);
