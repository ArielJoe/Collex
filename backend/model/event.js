import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const eventSchema = Schema({
    name: { type: String, required: true },
    location: { type: String, required: true },
    poster_url: { type: String },
    max_participants: { type: Number, required: true },
    organizer: { type: mongoose.Schema.Types.ObjectId, ref: 'User', required: true },
    faculty: { type: mongoose.Schema.Types.ObjectId, ref: 'Faculty', required: true },
    registration_deadline: { type: Date, required: true },
    created_at: { type: Date, default: Date.now },
    updated_at: { type: Date }
}, {
    collection: "event",
});

export default model('Event', eventSchema);
