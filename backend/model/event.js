import mongoose from "mongoose";

const eventSchema = new mongoose.Schema(
  {
    name: { type: String, required: true },
    date_time: { type: Date, required: true },
    location: { type: String, required: true },
    speaker: String,
    poster_url: String,
    registration_fee: { type: mongoose.Types.Decimal128, default: 0.0 },
    max_participants: { type: Number, required: true },
    organizer_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
  },
  {
    timestamps: { createdAt: "created_at", updatedAt: "updated_at" },
    collection: "event",
  }
);

export default mongoose.model("Event", eventSchema);
