import mongoose from "mongoose";

const attendanceSchema = new mongoose.Schema(
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
    scanned_by: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User",
      required: true,
    },
    scanned_at: { type: Date, default: Date.now },
  },
  {
    collection: "attendance",
  }
);

export default mongoose.model("Attendance", attendanceSchema);
