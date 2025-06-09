import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const RegistrationSchema = Schema({
    user_id: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    event_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Event', required: true },
    detail_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventDetail' },
    package_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventPackage' },
    payment_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Payment', required: true },
    registration_date: { type: Date, default: Date.now },
}, {
    collection: "registration",
});

export default model('Registration', RegistrationSchema);
