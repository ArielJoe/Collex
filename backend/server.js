import express from "express";
import { connectDB } from "./config/db.js";
import dotenv from "dotenv";
import cors from "cors"; // Add CORS to allow frontend requests
import authRoutes from "./routes/auth.js";
import userRoutes from "./routes/user.js";
import eventRoutes from "./routes/event.js";
import cartRoutes from "./routes/cart.js";
import paymentRoutes from "./routes/payment.js";
import registrationRoutes from "./routes/registration.js";
import facultyRoutes from "./routes/faculty.js";
import attendanceRoutes from "./routes/attendance.js";
import certificatesRoutes from "./routes/certificates.js";

dotenv.config();

const app = express();

app.use(express.json()); // Parse JSON bodies
app.use(cors()); // Enable CORS for all routes

// Root endpoint
app.get("/", (req, res) => {
  res.send("Server is ready");
});

app.use("/api/auth", authRoutes);
app.use("/api/user", userRoutes);
app.use("/api/event", eventRoutes);
app.use("/api/cart", cartRoutes);
app.use("/api/payment", paymentRoutes);
app.use("/api/registration", registrationRoutes);
app.use("/api/faculty", facultyRoutes);
app.use("/api/attendance", attendanceRoutes);
app.use("/api/certificates", certificatesRoutes);

const PORT = 5000;

app.listen(PORT, () => {
  connectDB();
  console.log(`Server started at http://localhost:${PORT}`);
});
