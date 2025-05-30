import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const CartSchema = Schema({
    user_id: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    event_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Event', required: true },
    detail_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventDetail' },
    package_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventPackage' },
    added_at: { type: Date, default: Date.now }
}, {
    collection: "cart",
});

export default model('Cart', CartSchema);
