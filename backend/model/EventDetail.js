import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const EventDetailSchema = Schema({
    event_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Event', required: true },
    title: { type: String, required: true },
    start_time: { type: Date, required: true },
    end_time: { type: Date, required: true },
    location: { type: String, required: true },
    speaker: { type: String, required: true },
    description: { type: String, required: true },
    price: { type: mongoose.Schema.Types.Decimal128 },
}, {
    collection: "event_detail",
});

export default model('EventDetail', EventDetailSchema);
