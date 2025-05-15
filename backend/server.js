import express from "express";
import { connectDB } from "./config/db.js";
import dotenv from "dotenv";
import cors from "cors"; // Add CORS to allow frontend requests
import authRoutes from "./routes/auth.js";

dotenv.config();

const app = express();

// Middleware
app.use(express.json()); // Parse JSON bodies
app.use(cors()); // Enable CORS for all routes

// Root endpoint
app.get("/", (req, res) => {
  res.send("Server is ready");
});

app.use("/api", authRoutes);

const PORT = 5000;

app.listen(PORT, () => {
  connectDB();
  console.log(`Server started at http://localhost:${PORT}`);
});
