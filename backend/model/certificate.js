import mongoose from "mongoose";

const certificateSchema = new mongoose.Schema(
  {
    registration_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "Registration",
      required: true,
    },
    event_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "Event",
      required: true,
    },
    certificate_url: { type: String, required: true },
    uploaded_by: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    uploaded_at: { type: Date, default: Date.now },
  },
  {
    collection: "certificate",
  }
);

export default mongoose.model("Certificate", certificateSchema);
