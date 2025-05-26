import mongoose from "mongoose";

const paymentSchema = new mongoose.Schema(
  {
    registration_id: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "Registration",
      required: true,
    },
    proof_url: { type: String },
    amount: { type: mongoose.Types.Decimal128, required: true },
    status: {
      type: String,
      enum: ["pending", "confirmed", "rejected"],
      default: "pending",
    },
    confirmed_by: { type: mongoose.Schema.Types.ObjectId, ref: "User" },
    confirmed_at: { type: Date },
  },
  {
    timestamps: { createdAt: "created_at" },
    collection: "payment",
  }
);

export default mongoose.model("Payment", paymentSchema);
