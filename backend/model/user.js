import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const UserSchema = Schema({
    email: { type: String, required: true, unique: true },
    password: { type: String, required: true },
    full_name: { type: String, required: true },
    phone_number: { type: String },
    role: {
        type: String,
        enum: ['member', 'admin', 'finance', 'organizer'],
        required: true
    },
    is_active: { type: Boolean, default: true }
}, {
    collection: "user",
});

export default model('User', UserSchema);
