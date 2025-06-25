import mongoose from "mongoose";
import { faker } from "@faker-js/faker";
import dotenv from "dotenv";
import bcrypt from "bcryptjs";
import crypto from "crypto";

// Adjust the import path based on your project structure
import { connectDB } from "../config/db.js";

import Faculty from "../model/Faculty.js";
import User from "../model/User.js";
import Event from "../model/Event.js";
import EventDetail from "../model/EventDetail.js";
import EventPackage from "../model/EventPackage.js";
import Registration from "../model/Registration.js";
import Attendance from "../model/Attendance.js";
import Cart from "../model/Cart.js";
import Certificate from "../model/Certificate.js";
import Payment from "../model/Payment.js";

dotenv.config();

// Encryption function from your router (ensure consistency)
const ENCRYPTION_KEY = process.env.QR_ENCRYPTION_KEY || crypto.randomBytes(32).toString('hex'); // Default to random key if not set
const ALGORITHM = 'aes-256-cbc';

function encrypt(text) {
    const iv = crypto.randomBytes(16);
    const cipher = crypto.createCipheriv(ALGORITHM, Buffer.from(ENCRYPTION_KEY, 'hex'), iv);
    let encrypted = cipher.update(text, 'utf8', 'base64');
    encrypted += cipher.final('base64');
    return `${encrypted}:${iv.toString('base64')}`;
}

// Helper function for safe date generation
function getSafeDateBetween(from, to, minDuration = 30 * 60 * 1000) {
    if (from >= to) {
        to = new Date(from.getTime() + minDuration);
    }
    return faker.date.between({ from, to });
}

const clearDatabase = async () => {
    console.log("Clearing existing data...");
    const collections = [
        Faculty,
        User,
        Event,
        EventDetail,
        EventPackage,
        Registration,
        Attendance,
        Cart,
        Certificate,
        Payment
    ];
    for (const model of collections) {
        try {
            await model.deleteMany({});
            console.log(`Cleared ${model.modelName} collection.`);
        } catch (error) {
            console.warn(`Could not clear ${model.modelName} collection: ${error.message}`);
        }
    }
    console.log("Database cleared (or was empty).");
};

const seedDatabase = async () => {
    try {
        await connectDB();
        await clearDatabase();

        const currentDate = new Date("2025-06-24T08:18:00+07:00"); // Updated to current date and time

        // --- 1. Seed Faculties ---
        console.log("Seeding faculties...");
        const createdFaculties = await Faculty.insertMany([
            { name: 'Fakultas Kedokteran', code: 'FK' },
            { name: 'Fakultas Kedokteran Gigi', code: 'FKG' },
            { name: 'Fakultas Psikologi', code: 'FP' },
            { name: 'Fakultas Teknologi dan Rekayasa Cerdas', code: 'FTRC' },
            { name: 'Fakultas Humaniora dan Industri Kreatif', code: 'FHIK' },
            { name: 'Fakultas Hukum dan Bisnis Digital', code: 'FHBD' }
        ]);
        console.log(`${createdFaculties.length} faculties seeded.`);

        // --- 2. Seed Users ---
        console.log("Seeding users...");
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash("123", salt);

        const usersToCreate = [
            {
                email: "member@example.com",
                password: hashedPassword,
                full_name: "Member User",
                phone_number: faker.phone.number(),
                photo_url: faker.image.avatar(),
                role: "member",
                is_active: true,
            },
            {
                email: "admin@example.com",
                password: hashedPassword,
                full_name: "Admin User",
                phone_number: faker.phone.number(),
                photo_url: faker.image.avatar(),
                role: "admin",
                is_active: true,
            },
            {
                email: "finance@example.com",
                password: hashedPassword,
                full_name: "Finance User",
                phone_number: faker.phone.number(),
                photo_url: faker.image.avatar(),
                role: "finance",
                is_active: true,
            },
            {
                email: "organizer@example.com",
                password: hashedPassword,
                full_name: "Organizer User",
                phone_number: faker.phone.number(),
                photo_url: faker.image.avatar(),
                role: "organizer",
                is_active: true,
            }
        ];
        const createdUsers = await User.insertMany(usersToCreate);
        console.log(`${createdUsers.length} users seeded.`);

        const adminUsers = createdUsers.filter(u => u.role === "admin");
        const organizerUsers = createdUsers.filter(u => u.role === "organizer");
        const financeUsers = createdUsers.filter(u => u.role === "finance");
        const memberUsers = createdUsers.filter(u => u.role === "member");

        const defaultOrganizer = organizerUsers[0];
        const defaultScannerOrUploader = adminUsers[0];
        const defaultFinanceConfirmer = financeUsers[0];

        // --- 3. Seed Events ---
        console.log("Seeding events...");
        const eventsData = [
            {
                name: "Past Tech Summit",
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlPicsumPhotos(),
                registered_participant: mongoose.Types.Decimal128.fromString("0"),
                max_participant: 200,
                organizer: defaultOrganizer._id,
                faculty: faker.helpers.arrayElement(createdFaculties)._id,
                registration_deadline: faker.date.past({ years: 1, refDate: currentDate }),
                start_time: faker.date.past({ years: 1, refDate: currentDate }),
                end_time: faker.date.soon({ days: 1, refDate: faker.date.past({ years: 1, refDate: currentDate }) }),
            },
            {
                name: "Current Workshop",
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlPicsumPhotos(),
                registered_participant: mongoose.Types.Decimal128.fromString("0"),
                max_participant: 150,
                organizer: defaultOrganizer._id,
                faculty: faker.helpers.arrayElement(createdFaculties)._id,
                registration_deadline: faker.date.recent({ days: 5, refDate: currentDate }),
                start_time: faker.date.soon({ days: 1, refDate: currentDate }),
                end_time: faker.date.soon({ days: 2, refDate: currentDate }),
            },
            {
                name: "Future Conference",
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlPicsumPhotos(),
                registered_participant: mongoose.Types.Decimal128.fromString("0"),
                max_participant: 300,
                organizer: defaultOrganizer._id,
                faculty: faker.helpers.arrayElement(createdFaculties)._id,
                registration_deadline: faker.date.soon({ days: 30, refDate: currentDate }),
                start_time: faker.date.soon({ days: 31, refDate: currentDate }),
                end_time: faker.date.soon({ days: 32, refDate: currentDate }),
            }
        ];
        const createdEvents = await Event.insertMany(eventsData);
        console.log(`${createdEvents.length} events seeded.`);

        // --- 4. Seed Event Details ---
        console.log("Seeding event details...");
        const eventDetailsData = [];
        for (const event of createdEvents) {
            // Ensure minimum event duration of 2 hours
            if (event.end_time.getTime() - event.start_time.getTime() < 2 * 60 * 60 * 1000) {
                event.end_time = new Date(event.start_time.getTime() + 2 * 60 * 60 * 1000);
                await event.save();
            }

            // First session
            const detailStartTime1 = getSafeDateBetween(
                event.start_time,
                new Date(event.end_time.getTime() - 60 * 60 * 1000) // Leave 1 hour for second session
            );
            const detailEndTime1 = new Date(Math.min(
                detailStartTime1.getTime() + 60 * 60 * 1000,
                event.end_time.getTime()
            ));

            // Second session
            const detailStartTime2 = getSafeDateBetween(
                detailEndTime1,
                new Date(event.end_time.getTime() - 30 * 60 * 1000) // Leave 30 minutes buffer
            );
            const detailEndTime2 = new Date(Math.min(
                detailStartTime2.getTime() + 60 * 60 * 1000,
                event.end_time.getTime()
            ));

            eventDetailsData.push(
                {
                    event_id: event._id,
                    title: `${event.name} Session 1 (Pembukaan)`,
                    start_time: detailStartTime1,
                    end_time: detailEndTime1,
                    location: "Ruang A1 / " + faker.location.city(),
                    speaker: faker.person.fullName(),
                    description: faker.lorem.paragraphs(2),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 0, max: 250000, dec: 0 })),
                },
                {
                    event_id: event._id,
                    title: `${event.name} Session 2`,
                    start_time: detailStartTime2,
                    end_time: detailEndTime2,
                    location: "Ruang A2 / " + faker.location.city(),
                    speaker: faker.person.fullName(),
                    description: faker.lorem.paragraphs(2),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 0, max: 250000, dec: 0 })),
                }
            );
        }
        const createdEventDetails = await EventDetail.insertMany(eventDetailsData);
        console.log(`${createdEventDetails.length} event details seeded.`);

        // --- 5. Seed Event Packages ---
        console.log("Seeding event packages...");
        const eventPackagesData = [];
        for (const event of createdEvents) {
            eventPackagesData.push(
                {
                    event_id: event._id,
                    package_name: "Tiket Early Bird",
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 100000, max: 750000, dec: 0 })),
                    description: faker.lorem.sentences(2),
                },
                {
                    event_id: event._id,
                    package_name: "Paket Standar",
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 100000, max: 750000, dec: 0 })),
                    description: faker.lorem.sentences(2),
                }
            );
        }
        const createdEventPackages = await EventPackage.insertMany(eventPackagesData);
        console.log(`${createdEventPackages.length} event packages seeded.`);

        // --- 6. Seed Registrations & Payments ---
        console.log("Seeding registrations and payments...");
        const createdRegistrations = [];
        const createdPayments = [];

        const scenarios = [
            { user: memberUsers[0], event: createdEvents[0], detailId: createdEventDetails[0]._id, packageId: null, status: 'confirmed' },
            { user: memberUsers[0], event: createdEvents[1], detailId: createdEventDetails[3]._id, packageId: null, status: 'pending' },
            { user: memberUsers[0], event: createdEvents[2], detailId: null, packageId: createdEventPackages[4]._id, status: 'rejected' },
            { user: memberUsers[0], event: createdEvents[0], detailId: createdEventDetails[1]._id, packageId: createdEventPackages[1]._id, status: 'confirmed' },
        ];

        for (const scenario of scenarios) {
            const { user, event, detailId, packageId, status } = scenario;
            let itemPrice = mongoose.Types.Decimal128.fromString("0.00");
            if (detailId) {
                itemPrice = createdEventDetails.find(ed => ed._id.equals(detailId)).price;
            } else if (packageId) {
                itemPrice = createdEventPackages.find(ep => ep._id.equals(packageId)).price;
            }

            let confirmedBy = null;
            let confirmedAt = null;
            if (status === "confirmed") {
                confirmedBy = defaultFinanceConfirmer._id;
                confirmedAt = faker.date.recent({ days: 10, refDate: currentDate });
            }

            const payment = new Payment({
                proof_url: faker.image.urlLoremFlickr({ category: 'abstract' }),
                amount: itemPrice,
                status: status,
                confirmed_by: confirmedBy,
                confirmed_at: confirmedAt,
                user_id: user._id,
                created_at: faker.date.recent({ days: 30, refDate: currentDate }),
            });

            const registration = new Registration({
                user_id: user._id,
                event_id: event._id,
                detail_id: detailId,
                package_id: packageId,
                payment_id: payment._id,
                registration_date: faker.date.recent({ days: 30, refDate: currentDate }),
            });

            const savedPayment = await payment.save();
            const savedRegistration = await registration.save();

            if (status === 'confirmed') {
                await Event.findByIdAndUpdate(event._id, {
                    $inc: { registered_participant: 1 }
                });
            }

            createdPayments.push(savedPayment);
            createdRegistrations.push(savedRegistration);
        }
        console.log(`${createdRegistrations.length} registrations seeded.`);
        console.log(`${createdPayments.length} payments seeded.`);

        // --- 7. Seed Attendances ---
        console.log("Seeding attendances...");
        const attendancesData = [];
        for (const reg of createdRegistrations) {
            const paymentForReg = createdPayments.find(p => p._id.equals(reg.payment_id));
            if (paymentForReg && paymentForReg.status === 'confirmed' && reg.detail_id) {
                const eventDetail = createdEventDetails.find(ed => ed._id.equals(reg.detail_id));
                if (eventDetail) {
                    const scannedAt = getSafeDateBetween(eventDetail.start_time, eventDetail.end_time);
                    const qrPlaintext = `${reg._id}:${reg.detail_id}`;
                    const qrCode = encrypt(qrPlaintext);

                    attendancesData.push({
                        registration_id: reg._id,
                        detail_id: reg.detail_id,
                        qr_code: qrCode,
                        scanned_by: defaultScannerOrUploader._id,
                        scanned_at: scannedAt,
                    });
                }
            }
        }
        const createdAttendances = await Attendance.insertMany(attendancesData);
        console.log(`${createdAttendances.length} attendances seeded.`);

        // --- 8. Seed Certificates ---
        console.log("Seeding certificates...");
        const certificatesData = [];
        for (const att of createdAttendances) {
            certificatesData.push({
                registration_id: att.registration_id,
                detail_id: att.detail_id,
                certificate_url: "https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf", // ← online dummy PDF
                uploaded_by: defaultScannerOrUploader._id,
                uploaded_at: faker.date.recent({ days: 7, refDate: currentDate }),
            });
        }
        const createdCertificates = await Certificate.insertMany(certificatesData);
        console.log(`${createdCertificates.length} certificates seeded.`);

        // --- 9. Seed Carts ---
        console.log("Seeding carts...");
        const cartsData = [
            {
                user_id: memberUsers[0]._id,
                event_id: createdEvents[1]._id,
                detail_id: createdEventDetails[3]._id,
                package_id: null,
                added_at: faker.date.recent({ days: 7, refDate: currentDate }),
            },
            {
                user_id: memberUsers[0]._id,
                event_id: createdEvents[2]._id,
                detail_id: null,
                package_id: createdEventPackages[4]._id,
                added_at: faker.date.recent({ days: 7, refDate: currentDate }),
            }
        ];
        const createdCarts = await Cart.insertMany(cartsData);
        console.log(`${createdCarts.length} carts seeded.`);

        console.log("Database seeding completed successfully!");

    } catch (err) {
        console.error("Seeding failed catastrophically:", err);
    } finally {
        if (mongoose.connection.readyState === 1) {
            await mongoose.disconnect();
            console.log("Database disconnected.");
        }
    }
};

seedDatabase();