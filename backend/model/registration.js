import mongoose from "mongoose";

const registrationSchema = new mongoose.Schema(
  {
    user_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    event_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "Event",
      required: true,
    },
    qr_code: { type: String, required: true, unique: true },
    payment_status: {
      type: String,
      enum: ["pending", "emission", "rejected"],
      default: "pending",
    },
    registration_date: { type: Date, default: Date.now },
  },
  {
    timestamps: { createdAt: "created_at" },
    collection: "registration",
  }
);

export default mongoose.model("Registration", registrationSchema);
