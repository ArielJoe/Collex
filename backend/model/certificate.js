import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const CertificateSchema = Schema({
    registration_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Registration', required: true },
    detail_id: { type: mongoose.Schema.Types.ObjectId, ref: 'EventDetail', required: true },
    certificate_url: { type: String, required: true },
    uploaded_by: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    uploaded_at: { type: Date, default: Date.now }
}, {
    collection: "certificate",
});

export default model('Certificate', CertificateSchema);
