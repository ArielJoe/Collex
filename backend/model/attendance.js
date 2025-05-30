import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const AttendanceSchema = Schema({
    registration_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Registration', required: true },
    detail_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventDetail', required: true },
    qr_code: { type: String, required: true, unique: true },
    scanned_by: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    scanned_at: { type: Date, default: Date.now }
}, {
    collection: "attendance",
});

export default model('Attendance', AttendanceSchema);
