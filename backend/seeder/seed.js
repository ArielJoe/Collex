import mongoose from "mongoose";
import { faker } from "@faker-js/faker";
import dotenv from "dotenv";
import bcrypt from "bcryptjs";

import User from "../model/User.js";
import Event from "../model/event.js";
import Registration from "../model/registration.js";
import Payment from "../model/payment.js";
import Attendance from "../model/attendance.js";
import Certificate from "../model/certificate.js";

dotenv.config();

mongoose
  .connect(process.env.MONGO_URI, {
    useNewUrlParser: true,
    useUnifiedTopology: true,
  })
  .then(() => {
    console.log("Database connected!");
    seedDatabase();
  })
  .catch((err) => {
    console.error("Database connection failed", err);
  });

const seedDatabase = async () => {
  try {
    await mongoose.connection.db.dropDatabase();

    const users = await User.insertMany([
      {
        email: "admin@example.com",
        password: await bcrypt.hash("admin123", 10),
        full_name: "Admin User",
        phone_number: faker.phone.number(),
        role: "admin",
        is_active: true,
      },
      {
        email: "organizer@example.com",
        password: "organizer123",
        full_name: "Event Organizer",
        phone_number: faker.phone.number(),
        role: "organizer",
        is_active: true,
      },
      {
        email: "member@example.com",
        password: "member123",
        full_name: "Event Member",
        phone_number: faker.phone.number(),
        role: "member",
        is_active: true,
      },
      {
        email: "finance@example.com",
        password: "finance123",
        full_name: "Finance User",
        phone_number: faker.phone.number(),
        role: "finance",
        is_active: true,
      },
      {
        email: "guest@example.com",
        password: "guest123",
        full_name: "Guest User",
        phone_number: faker.phone.number(),
        role: "guest",
        is_active: true,
      },
    ]);

    console.log("Users seeded!");

    const events = await Event.insertMany([
      {
        name: "Tech Conference 2025",
        date_time: new Date(),
        location: "Tech Center",
        speaker: "John Doe",
        poster_url: faker.image.url(),
        registration_fee: 100,
        max_participants: 200,
        organizer_id: users[1]._id,
      },
      {
        name: "Web Development Workshop",
        date_time: new Date(),
        location: "Dev Hub",
        speaker: "Jane Smith",
        poster_url: faker.image.url(),
        registration_fee: 50,
        max_participants: 100,
        organizer_id: users[1]._id,
      },
    ]);

    console.log("Events seeded!");

    const registrations = await Registration.insertMany([
      {
        user_id: users[2]._id,
        event_id: events[0]._id,
        qr_code: faker.string.uuid(),
      },
      {
        user_id: users[2]._id,
        event_id: events[1]._id,
        qr_code: faker.string.uuid(),
      },
    ]);

    console.log("Registrations seeded!");

    const payments = await Payment.insertMany([
      {
        registration_id: registrations[0]._id,
        proof_url: faker.image.url(),
        amount: 100,
        status: "pending",
      },
      {
        registration_id: registrations[1]._id,
        proof_url: faker.image.url(),
        amount: 50,
        status: "confirmed",
        confirmed_by: users[3]._id,
        confirmed_at: new Date(),
      },
    ]);

    console.log("Payments seeded!");

    const attendances = await Attendance.insertMany([
      {
        registration_id: registrations[0]._id,
        event_id: events[0]._id,
        scanned_by: users[1]._id,
      },
      {
        registration_id: registrations[1]._id,
        event_id: events[1]._id,
        scanned_by: users[1]._id,
      },
    ]);

    console.log("Attendances seeded!");

    const certificates = await Certificate.insertMany([
      {
        registration_id: registrations[0]._id,
        event_id: events[0]._id,
        certificate_url: faker.image.url(),
        uploaded_by: users[1]._id,
      },
      {
        registration_id: registrations[1]._id,
        event_id: events[1]._id,
        certificate_url: faker.image.url(),
        uploaded_by: users[1]._id,
      },
    ]);

    console.log("Certificates seeded!");
    mongoose.disconnect();
  } catch (err) {
    console.error("Seeding failed", err);
    mongoose.disconnect();
  }
};
