import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const eventSchema = Schema({
    name: { type: String, required: true },
    location: { type: String, required: true },
    poster_url: { type: String },
    registered_participant: { type: mongoose.Types.Decimal128, default: 0 },
    max_participant: { type: Number, required: true },
    organizer: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    faculty: { type: mongoose.Schema.Types.ObjectId, ref: 'Faculty', required: true },
    registration_deadline: { type: Date, required: true },
    start_time: { type: Date, required: true },
    end_time: { type: Date, required: true },
    created_at: { type: Date, default: Date.now },
}, {
    collection: "event",
});

export default model('Event', eventSchema);
