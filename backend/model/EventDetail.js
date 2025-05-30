import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const EventDetailSchema = Schema({
    event_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Event', required: true },
    session_title: { type: String, required: true },
    session_date: { type: Date, required: true },
    start_time: { type: String, required: true },
    end_time: { type: String, required: true },
    location: { type: String, required: true },
    speaker: { type: String, required: true },
    description: { type: String, required: true },
    price: { type: mongoose.Schema.Types.Decimal128 },
}, {
    collection: "event_detail",
});

export default model('EventDetail', EventDetailSchema);
