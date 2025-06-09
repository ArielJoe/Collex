import mongoose from "mongoose";
import { faker } from "@faker-js/faker";
import dotenv from "dotenv";
import bcrypt from "bcryptjs";

// Assuming connectDB is correctly exported from this path
// Adjust if your file is named index.js or is directly in config
import { connectDB } from "../config/db.js";

// Model imports - ensure these paths and filenames match your project structure
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
                email: faker.internet.email({ firstName: faker.person.firstName(), lastName: faker.person.lastName() }).toLowerCase(),
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
            let eventEndTime;
            if (faker.datatype.boolean()) {
                eventEndTime = faker.date.soon({ hours: faker.number.int({ min: 2, max: 8 }), refDate: eventStartTime });
            } else {
                const durationDays = faker.number.int({ min: 0, max: 2 });
                if (durationDays === 0) {
                    eventEndTime = faker.date.soon({ hours: faker.number.int({ min: 2, max: 12 }), refDate: eventStartTime });
                } else {
                    eventEndTime = faker.date.soon({ days: durationDays, refDate: eventStartTime });
                }
                if (eventEndTime.toDateString() === eventStartTime.toDateString() && eventEndTime <= eventStartTime) {
                    eventEndTime = new Date(eventStartTime.getTime() + faker.number.int({ min: 2, max: 8 }) * 60 * 60 * 1000);
                }
            }
            if (eventEndTime <= eventStartTime) {
                eventEndTime = new Date(eventStartTime.getTime() + faker.number.int({ min: 2, max: 8 }) * 60 * 60 * 1000);
            }
            eventsData.push({
                name: faker.company.catchPhrase() + " " + faker.helpers.arrayElement(["Summit", "Workshop", "Conference", "Fest"]),
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlPicsumPhotos(),
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
                    if (detailEndTime > event.end_time) detailEndTime = event.end_time;
                }
                if (detailStartTime >= detailEndTime && detailStartTime < event.end_time) {
                    detailStartTime = new Date(event.start_time.getTime() + i * 60 * 60 * 1000);
                    detailEndTime = new Date(detailStartTime.getTime() + faker.number.int({ min: 1, max: 3 }) * 60 * 60 * 1000);
                    if (detailEndTime > event.end_time) detailEndTime = event.end_time;
                    if (detailStartTime >= detailEndTime) detailStartTime = new Date(event.end_time.getTime() - 60 * 60 * 1000);
                }
                if (detailStartTime >= detailEndTime) {
                    console.warn(`Could not generate valid time for event detail for event ${event._id}. Skipping this detail.`);
                    continue;
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
        let createdEventDetails = [];
        if (eventDetailsData.length > 0) {
            createdEventDetails = await EventDetail.insertMany(eventDetailsData);
            console.log(`${createdEventDetails.length} event details seeded.`);
        } else {
            console.log("No event details seeded.");
        }

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
        let createdEventPackages = [];
        if (eventPackagesData.length > 0) {
            createdEventPackages = await EventPackage.insertMany(eventPackagesData);
            console.log(`${createdEventPackages.length} event packages seeded.`);
        } else {
            console.log("No event packages seeded.");
        }

        // --- 6. Seed Registrations & Payments (Integrated) ---
        console.log("Seeding registrations and payments...");
        const availableUsersForReg = memberUsers.length > 0 ? memberUsers : createdUsers.filter(u => u.role !== 'admin' && u.role !== 'organizer' && u.role !== 'finance');
        const allEventDetails = createdEventDetails;
        const createdRegistrations = [];
        const createdPayments = [];

        for (let i = 0; i < 50 && availableUsersForReg.length > 0 && createdEvents.length > 0; i++) {
            const user = faker.helpers.arrayElement(availableUsersForReg);
            const event = faker.helpers.arrayElement(createdEvents);
            let detailId = null;
            let packageId = null;
            let itemPrice = mongoose.Types.Decimal128.fromString("0.00");

            if (faker.datatype.boolean(0.7) && allEventDetails.length > 0) {
                const detailsForThisEvent = allEventDetails.filter(ed => ed.event_id.equals(event._id));
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

            if (!detailId && !packageId) { // Fallback jika tidak terpilih
                const detailsForThisEvent = allEventDetails.filter(ed => ed.event_id.equals(event._id));
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

                // Step 1: Create Registration first (without saving to get _id)
                const registration = new Registration({
                    user_id: user._id,
                    event_id: event._id,
                    detail_id: detailId,
                    package_id: packageId,
                    // payment_id will be set after payment is created
                });

                // Step 2: Create Payment with the registration _id
                const payment = new Payment({
                    proof_url: faker.image.urlLoremFlickr({ category: 'abstract' }),
                    amount: itemPrice,
                    status: paymentStatus,
                    confirmed_by: confirmedBy,
                    confirmed_at: confirmedAt,
                    user_id: user._id,
                });

                // Step 3: Set payment_id in registration
                registration.payment_id = payment._id;

                // Step 4: Save both documents
                const savedPayment = await payment.save();
                const savedRegistration = await registration.save();

                createdPayments.push(savedPayment);
                createdRegistrations.push(savedRegistration);
            }
        }

        console.log(`${createdRegistrations.length} registrations seeded.`);
        console.log(`${createdPayments.length} payments seeded.`);

        // --- 8. Seed Attendances ---
        console.log("Seeding attendances...");
        const attendancesData = [];
        for (const reg of createdRegistrations) {
            const paymentForReg = createdPayments.find(p => p._id.equals(reg.payment_id));
            if (paymentForReg && paymentForReg.status === 'confirmed' && reg.detail_id) {
                const eventDetail = allEventDetails.find(ed => ed._id.equals(reg.detail_id));
                if (eventDetail && faker.datatype.boolean(0.85)) {
                    let scanTime = faker.date.between({ from: eventDetail.start_time, to: eventDetail.end_time });
                    if (scanTime < eventDetail.start_time || scanTime > eventDetail.end_time) {
                        scanTime = eventDetail.start_time;
                    }
                    attendancesData.push({
                        registration_id: reg._id,
                        detail_id: reg.detail_id,
                        qr_code: faker.string.uuid(),
                        scanned_by: defaultScannerOrUploader._id,
                        scanned_at: scanTime,
                    });
                }
            }
        }

        // --- 9. Seed Certificates ---
        // console.log("Seeding certificates...");
        // const certificatesData = [];
        // for (const att of createdAttendances) {
        //     certificatesData.push({
        //         registration_id: att.registration_id,
        //         detail_id: att.detail_id,
        //         certificate_url: faker.internet.url() + `/certificates/${att.registration_id}-${att.detail_id}.pdf`,
        //         uploaded_by: defaultScannerOrUploader._id,
        //     });
        // }

        // --- 10. Seed Carts ---
        console.log("Seeding carts...");
        const cartsData = [];
        const usersForCart = memberUsers.length > 0 ? memberUsers.slice(0, 10) : createdUsers.filter(u => u.role === 'member').slice(0, 10);
        for (const user of usersForCart) {
            if (faker.datatype.boolean(0.5) && createdEvents.length > 0) {
                const event = faker.helpers.arrayElement(createdEvents);
                let detailId = null;
                let packageId = null;
                if (faker.datatype.boolean() && allEventDetails.length > 0) {
                    const detailsForThisEvent = allEventDetails.filter(ed => ed.event_id.equals(event._id));
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
                    });
                }
            }
        }
        if (cartsData.length > 0) {
            await Cart.insertMany(cartsData);
            console.log(`${cartsData.length} carts seeded.`);
        } else {
            console.log("No carts seeded.");
        }

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
