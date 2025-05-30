import { Schema, model } from 'mongoose';
import mongoose from 'mongoose';

const FacultySchema = Schema({
    name: { type: String, required: true },
    code: { type: String, required: true, unique: true },
}, {
    collection: "faculty",
});

export default model('Faculty', FacultySchema);
