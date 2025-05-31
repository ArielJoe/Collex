import express from "express";
import bcrypt from "bcryptjs";
import User from "../model/User.js"; // Path ke model User

const router = express.Router();

// Login Endpoint
router.post("/login", async (req, res) => {
  const { email, password } = req.body;
  try {
    const user = await User.findOne({ email });

    if (!user) {
      return res.status(401).json({ message: "Invalid credentials - user not found" });
    }

    if (!user.is_active) {
      return res.status(403).json({ message: "User account is inactive." });
    }

    const isMatch = await bcrypt.compare(password, user.password);

    if (!isMatch) {
      return res.status(401).json({ message: "Invalid credentials - password mismatch" });
    }

    // Tidak ada session handling di sini, hanya mengembalikan data user
    // Jika Anda menggunakan JWT, token akan dibuat dan dikirim di sini.
    res.status(200).json({
      message: "Login successful",
      user: {
        id: user._id,
        email: user.email,
        full_name: user.full_name,
        role: user.role,
        photo_url: user.photo_url, // Mengembalikan photo_url jika ada
      },
    });
  } catch (err) {
    console.error("Error during login:", err);
    res.status(500).json({ message: "Server error during login" });
  }
});

// Registration Endpoint
router.post("/register", async (req, res) => {
  const { email, password, full_name, phone_number, role, photo_url } = req.body;

  // Validasi input dasar
  if (!email || !password || !full_name || !phone_number || !role) {
    return res
      .status(400)
      .json({
        message: "Email, password, full name, phone number, and role are required",
      });
  }

  // Validasi format email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return res.status(400).json({ message: "Invalid email format" });
  }

  // Validasi peran
  const allowedRoles = ['member', 'admin', 'finance', 'organizer']; // Sesuai UserSchema baru
  if (!allowedRoles.includes(role)) {
    return res.status(400).json({ message: `Invalid role. Allowed roles are: ${allowedRoles.join(', ')}` });
  }

  try {
    // Cek apakah user sudah ada
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: "Email already registered" });
    }

    // Hash password
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    // Buat user baru
    const newUser = new User({
      email,
      password: hashedPassword,
      full_name,
      phone_number,
      role: role, // role sudah divalidasi
      photo_url: photo_url || null, // photo_url opsional
      is_active: true, // Default is_active dari schema adalah true
    });

    // Simpan user
    await newUser.save();

    res.status(201).json({ message: "Registration successful", userId: newUser._id });
  } catch (error) {
    console.error("Registration error:", error);
    if (error.code === 11000) { // Error duplikasi MongoDB
      return res.status(400).json({ message: "Email or other unique field already exists." });
    }
    res.status(500).json({ message: "Server error during registration" });
  }
});

export default router;
