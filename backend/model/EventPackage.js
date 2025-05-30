import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const EventPackageSchema = Schema({
    event_id: { type: mongoose.Schema.Types.ObjectId, ref: 'Event', required: true },
    package_name: { type: String, required: true },
    price: { type: mongoose.Schema.Types.Decimal128, required: true },
    description: { type: String },
}, {
    collection: "event_package",
});

export default model('EventPackage', EventPackageSchema);
