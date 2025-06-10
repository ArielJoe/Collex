import mongoose from "mongoose";
import { faker } from "@faker-js/faker";
import dotenv from "dotenv";
import bcrypt from "bcryptjs";

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

const clearDatabase = async () => {
    console.log("Clearing existing data...");
    const collections = mongoose.connection.collections;
    for (const key in collections) {
        const collection = collections[key];
        try {
            await collection.deleteMany({});
        } catch (error) {
            console.warn(`Could not clear collection ${key}: ${error.message}`);
        }
    }
    console.log("Database cleared (or was empty).");
};

const seedDatabase = async () => {
    try {
        await connectDB();
        await clearDatabase();

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
        const usersToCreate = [];
        const roles = ["member", "admin", "finance", "organizer"];
        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash("123", salt);

        for (let i = 0; i < 40; i++) {
            usersToCreate.push({
                email: faker.internet.email().toLowerCase(),
                password: hashedPassword,
                full_name: faker.person.fullName(),
                phone_number: faker.phone.number(),
                photo_url: faker.image.avatar(),
                role: faker.helpers.arrayElement(roles),
                is_active: faker.datatype.boolean(0.9),
            });
        }
        const criticalRolesToEnsure = ["admin", "finance", "organizer", "member"];
        for (const role of criticalRolesToEnsure) {
            if (!usersToCreate.some(u => u.role === role)) {
                usersToCreate.push({
                    email: `${role.toLowerCase().replace(/\s+/g, '')}@example.com`,
                    password: hashedPassword,
                    full_name: `${role.charAt(0).toUpperCase() + role.slice(1)} User`,
                    phone_number: faker.phone.number(),
                    photo_url: faker.image.avatar(),
                    role: role,
                    is_active: true,
                });
            }
        }
        const createdUsers = await User.insertMany(usersToCreate);
        console.log(`${createdUsers.length} users seeded.`);

        const adminUsers = createdUsers.filter(u => u.role === "admin");
        const organizerUsers = createdUsers.filter(u => u.role === "organizer");
        const financeUsers = createdUsers.filter(u => u.role === "finance");
        const memberUsers = createdUsers.filter(u => u.role === "member");

        const defaultOrganizer = organizerUsers.length > 0 ? organizerUsers[0] : (adminUsers.length > 0 ? adminUsers[0] : createdUsers[0]);
        const defaultScannerOrUploader = adminUsers.length > 0 ? adminUsers[0] : defaultOrganizer;
        const defaultFinanceConfirmer = financeUsers.length > 0 ? financeUsers[0] : defaultScannerOrUploader;

        // --- 3. Seed Events ---
        console.log("Seeding events...");
        const eventsData = [];
        for (let i = 0; i < 10; i++) {
            const deadlineDays = faker.number.int({ min: 7, max: 90 });
            const registrationDeadline = faker.date.soon({ days: deadlineDays });
            const eventStartTime = faker.date.soon({ days: faker.number.int({ min: 1, max: 7 }), refDate: registrationDeadline });
            const eventEndTime = faker.date.soon({ hours: faker.number.int({ min: 2, max: 12 }), refDate: eventStartTime });
            eventsData.push({
                name: faker.company.catchPhrase() + " " + faker.helpers.arrayElement(["Summit", "Workshop", "Conference", "Fest"]),
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlPicsumPhotos(),
                registered_participant: mongoose.Types.Decimal128.fromString("0"),
                max_participant: faker.number.int({ min: 50, max: 500 }),
                organizer: defaultOrganizer._id,
                faculty: faker.helpers.arrayElement(createdFaculties)._id,
                registration_deadline: registrationDeadline,
                start_time: eventStartTime,
                end_time: eventEndTime,
            });
        }
        const createdEvents = await Event.insertMany(eventsData);
        console.log(`${createdEvents.length} events seeded.`);

        // --- 4. Seed Event Details ---
        console.log("Seeding event details...");
        const eventDetailsData = [];
        for (const event of createdEvents) {
            const numberOfDetails = faker.number.int({ min: 1, max: 3 });
            for (let i = 0; i < numberOfDetails; i++) {
                let detailStartTime = faker.date.between({ from: event.start_time, to: event.end_time });
                let detailEndTime = faker.date.soon({ hours: faker.number.int({ min: 1, max: 4 }), refDate: detailStartTime });
                if (detailEndTime > event.end_time) detailEndTime = event.end_time;
                if (detailEndTime <= detailStartTime) {
                    detailEndTime = new Date(detailStartTime.getTime() + faker.number.int({ min: 1, max: 3 }) * 60 * 60 * 1000);
                }
                eventDetailsData.push({
                    event_id: event._id,
                    title: faker.lorem.sentence(4) + (i === 0 ? " (Pembukaan)" : ` (Sesi ${i + 1})`),
                    start_time: detailStartTime,
                    end_time: detailEndTime,
                    location: "Ruang " + faker.string.alphanumeric(3).toUpperCase() + " / " + faker.location.city(),
                    speaker: faker.person.fullName(),
                    description: faker.lorem.paragraphs(2),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 0, max: 250000, dec: 0 })),
                });
            }
        }
        const createdEventDetails = await EventDetail.insertMany(eventDetailsData);
        console.log(`${createdEventDetails.length} event details seeded.`);

        // --- 5. Seed Event Packages ---
        console.log("Seeding event packages...");
        const eventPackagesData = [];
        for (const event of createdEvents) {
            const numberOfPackages = faker.number.int({ min: 0, max: 2 });
            for (let i = 0; i < numberOfPackages; i++) {
                eventPackagesData.push({
                    event_id: event._id,
                    package_name: faker.helpers.arrayElement(["Tiket Early Bird", "Paket Standar", "Akses VIP"]) + (i > 0 ? ` Plus` : ''),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 100000, max: 750000, dec: 0 })),
                    description: faker.lorem.sentences(2),
                });
            }
        }
        const createdEventPackages = await EventPackage.insertMany(eventPackagesData);
        console.log(`${createdEventPackages.length} event packages seeded.`);

        // --- 6. Seed Registrations & Payments ---
        console.log("Seeding registrations and payments...");
        const availableUsersForReg = memberUsers.length > 0 ? memberUsers : createdUsers.filter(u => u.role !== 'admin' && u.role !== 'organizer' && u.role !== 'finance');
        const createdRegistrations = [];
        const createdPayments = [];

        for (let i = 0; i < 50 && availableUsersForReg.length > 0 && createdEvents.length > 0; i++) {
            const user = faker.helpers.arrayElement(availableUsersForReg);
            const event = faker.helpers.arrayElement(createdEvents);
            let detailId = null;
            let packageId = null;
            let itemPrice = mongoose.Types.Decimal128.fromString("0.00");

            if (faker.datatype.boolean(0.7) && createdEventDetails.length > 0) {
                const detailsForThisEvent = createdEventDetails.filter(ed => ed.event_id.equals(event._id));
                if (detailsForThisEvent.length > 0) {
                    const selectedDetail = faker.helpers.arrayElement(detailsForThisEvent);
                    detailId = selectedDetail._id;
                    itemPrice = selectedDetail.price;
                }
            } else if (createdEventPackages.length > 0) {
                const packagesForThisEvent = createdEventPackages.filter(ep => ep.event_id.equals(event._id));
                if (packagesForThisEvent.length > 0) {
                    const selectedPackage = faker.helpers.arrayElement(packagesForThisEvent);
                    packageId = selectedPackage._id;
                    itemPrice = selectedPackage.price;
                }
            }

            if (!detailId && !packageId) {
                const detailsForThisEvent = createdEventDetails.filter(ed => ed.event_id.equals(event._id));
                const packagesForThisEvent = createdEventPackages.filter(ep => ep.event_id.equals(event._id));
                if (detailsForThisEvent.length > 0) {
                    const selectedDetail = faker.helpers.arrayElement(detailsForThisEvent);
                    detailId = selectedDetail._id;
                    itemPrice = selectedDetail.price;
                } else if (packagesForThisEvent.length > 0) {
                    const selectedPackage = faker.helpers.arrayElement(packagesForThisEvent);
                    packageId = selectedPackage._id;
                    itemPrice = selectedPackage.price;
                }
            }

            if (detailId || packageId) {
                const paymentStatus = faker.helpers.arrayElement(['pending', 'confirmed', 'rejected']);
                let confirmedBy = null;
                let confirmedAt = null;

                if (paymentStatus === "confirmed") {
                    confirmedBy = defaultFinanceConfirmer._id;
                    confirmedAt = faker.date.recent({ days: 10 });
                }

                const payment = new Payment({
                    proof_url: faker.image.urlLoremFlickr({ category: 'abstract' }),
                    amount: itemPrice,
                    status: paymentStatus,
                    confirmed_by: confirmedBy,
                    confirmed_at: confirmedAt,
                    user_id: user._id, // Added user_id
                    created_at: faker.date.recent({ days: 30 }),
                });

                const registration = new Registration({
                    user_id: user._id,
                    event_id: event._id,
                    detail_id: detailId,
                    package_id: packageId,
                    payment_id: payment._id,
                    registration_date: faker.date.recent({ days: 30 }),
                });

                const savedPayment = await payment.save();
                const savedRegistration = await registration.save();

                createdPayments.push(savedPayment);
                createdRegistrations.push(savedRegistration);
            }
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
                if (eventDetail && faker.datatype.boolean(0.85)) {
                    let scannedAt = faker.date.between({ from: eventDetail.start_time, to: eventDetail.end_time });
                    if (scannedAt < eventDetail.start_time || scannedAt > eventDetail.end_time) {
                        scannedAt = eventDetail.start_time;
                    }
                    attendancesData.push({
                        registration_id: reg._id,
                        detail_id: reg.detail_id,
                        qr_code: faker.string.uuid(),
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
                certificate_url: faker.internet.url() + `/certificates/${att.registration_id}-${att.detail_id}.pdf`,
                uploaded_by: defaultScannerOrUploader._id,
                uploaded_at: faker.date.recent({ days: 7 }),
            });
        }
        const createdCertificates = await Certificate.insertMany(certificatesData);
        console.log(`${createdCertificates.length} certificates seeded.`);

        // --- 9. Seed Carts ---
        console.log("Seeding carts...");
        const cartsData = [];
        const usersForCart = memberUsers.length > 0 ? memberUsers.slice(0, 10) : createdUsers.filter(u => u.role === 'member').slice(0, 10);
        for (const user of usersForCart) {
            if (faker.datatype.boolean(0.5) && createdEvents.length > 0) {
                const event = faker.helpers.arrayElement(createdEvents);
                let detailId = null;
                let packageId = null;
                if (faker.datatype.boolean() && createdEventDetails.length > 0) {
                    const detailsForThisEvent = createdEventDetails.filter(ed => ed.event_id.equals(event._id));
                    if (detailsForThisEvent.length > 0) {
                        detailId = faker.helpers.arrayElement(detailsForThisEvent)._id;
                    }
                } else if (createdEventPackages.length > 0) {
                    const packagesForThisEvent = createdEventPackages.filter(ep => ep.event_id.equals(event._id));
                    if (packagesForThisEvent.length > 0) {
                        packageId = faker.helpers.arrayElement(packagesForThisEvent)._id;
                    }
                }
                if (detailId || packageId) {
                    cartsData.push({
                        user_id: user._id,
                        event_id: event._id,
                        detail_id: detailId,
                        package_id: packageId,
                        added_at: faker.date.recent({ days: 14 }),
                    });
                }
            }
        }
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
