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
        password: await bcrypt.hash("123", 10),
        full_name: "Admin User",
        phone_number: faker.phone.number(),
        role: "admin",
        is_active: true,
      },
      {
        email: "organizer@example.com",
        password: await bcrypt.hash("123", 10),
        full_name: "Event Organizer",
        phone_number: faker.phone.number(),
        role: "organizer",
        is_active: true,
      },
      {
        email: "member@example.com",
        password: await bcrypt.hash("123", 10),
        full_name: "Event Member",
        phone_number: faker.phone.number(),
        role: "member",
        is_active: true,
      },
      {
        email: "finance@example.com",
        password: await bcrypt.hash("123", 10),
        full_name: "Finance User",
        phone_number: faker.phone.number(),
        role: "finance",
        is_active: true,
      },
      {
        email: "guest@example.com",
        password: await bcrypt.hash("123", 10),
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
        start_time: new Date("2025-09-14T09:00:00"),
        end_time: new Date("2025-09-15T09:00:00"),
        location: "Tech Center, Main Hall",
        speaker: "John Doe",
        poster_url: faker.image.url(),
        registration_fee: 100,
        max_participants: 200,
        organizer_id: users[1]._id,
        description: `Join us for the biggest tech conference of the year! Tech Conference 2025 brings together industry leaders, innovators, and enthusiasts for three days of cutting-edge presentations, workshops, and networking opportunities.

Key Highlights:
- Keynote speeches from top tech CEOs
- Hands-on workshops on AI, blockchain, and cloud computing
- Startup pitch competitions with $50,000 in prizes
- Exclusive networking sessions with industry experts

Who Should Attend:
- Software developers and engineers
- Tech entrepreneurs and startup founders
- IT managers and decision-makers
- Students and tech enthusiasts

Don't miss this opportunity to stay ahead of the technology curve and connect with like-minded professionals!`
      },
      {
        name: "Web Development Workshop",
        start_time: new Date("2025-09-19T09:00:00"),
        end_time: new Date("2025-05-20T13:00:00"),
        location: "Dev Hub, Room 302",
        speaker: "Jane Smith",
        poster_url: faker.image.url(),
        registration_fee: 50,
        max_participants: 100,
        organizer_id: users[1]._id,
        description: `Master modern web development in this intensive one-day workshop led by renowned developer Jane Smith.

Workshop Topics:
- Building responsive UIs with React 19
- Server-side rendering with Next.js
- API development with Node.js and Express
- Database integration (MongoDB & PostgreSQL)
- Deployment strategies and CI/CD pipelines

What You'll Get:
- Hands-on coding exercises
- Comprehensive workshop materials
- Certificate of completion
- 1-month free access to our online learning platform

Prerequisites:
Basic knowledge of HTML, CSS, and JavaScript is recommended. Bring your laptop with Node.js installed.

Perfect for junior developers looking to level up their skills or experienced developers wanting to refresh their knowledge!`
      },
      {
        name: "Peloton 50: Ride the Legacy",
        start_time: new Date("2025-05-30T08:00:00"),
        end_time: new Date("2025-06-01T08:00:00"),
        location: "Celebrity Fitness Lupo Jogja",
        speaker: "Marco Rodriguez",
        poster_url: faker.image.url(),
        registration_fee: 75,
        max_participants: 50,
        organizer_id: users[0]._id,
        description: `CHAMPIONS, THIS IS YOUR CALL TO RIDE!

Peloton 50 is a special indoor cycling event that captures the spirit of Tour de France with competitive energy! This isn't just a workout - it's an experience of determination, sweat, and energy.

WHAT TO EXPECT:
- High-energy instructors who motivate you to push harder
- Challenging stages that test your endurance
- Studio atmosphere that keeps your competitive spirit alive
- Leaderboard tracking for the ultimate challenge
- Post-ride recovery zone with refreshments

PUSH PAST YOUR LIMITS, PEDAL WITH PURPOSE.
IT'S TIME FOR BIORVOLUTION!

Date: Sunday, 1 June 2025
Time: 8:00 AM - 12:00 PM
Location: Celebrity Fitness Lupo Jogja

Open to all fitness levels - modify to your ability!`
      }
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
