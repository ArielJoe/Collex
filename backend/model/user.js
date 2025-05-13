import mongoose from "mongoose";

const userSchema = new mongoose.Schema(
  {
    email: { type: String, required: true, unique: true },
    password: { type: String, required: true },
    full_name: { type: String, required: true },
    phone_number: String,
    role: {
      type: String,
      enum: ["guest", "member", "admin", "finance", "organizer"],
      required: true,
    },
    is_active: { type: Boolean, default: true },
  },
  {
    timestamps: { createdAt: "created_at", updatedAt: "updated_at" },
    collection: "user",
  }
);

export default mongoose.model("User", userSchema);
