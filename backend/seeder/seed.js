import mongoose from "mongoose";
import { faker } from "@faker-js/faker";
import dotenv from "dotenv";
import bcrypt from "bcryptjs";

// Assuming connectDB is correctly exported from this path
// If db.js is in config, it might be "../config/db.js"
// Adjust if your file is named index.js or is directly in config
import { connectDB } from "../config/db.js";

// Model imports - adjust paths and filenames if they differ from your image
// Assuming model files are in '../model/' and have capitalized names like 'Faculty.js'
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

// MONGO_URI check is already in your base, so it's fine.

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
        await connectDB(); // Ensure DB is connected before operations
        await clearDatabase(); // Clear existing data

        // --- 1. Seed Faculties ---
        console.log("Seeding faculties...");
        // Updated faculty data as per user request
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
        const hashedPassword = await bcrypt.hash("Password123!", salt);

        for (let i = 0; i < 40; i++) { // Create 40 users
            usersToCreate.push({
                email: faker.internet.email({ firstName: faker.person.firstName(), lastName: faker.person.lastName() }).toLowerCase(),
                password: hashedPassword,
                full_name: faker.person.fullName(),
                phone_number: faker.phone.number(),
                role: faker.helpers.arrayElement(roles),
                is_active: faker.datatype.boolean(0.9), // 90% active
                // created_at, updated_at will default or be set by Mongoose
            });
        }
        // Ensure at least one of each critical role
        const criticalRolesToEnsure = ["admin", "finance", "organizer"];
        for (const role of criticalRolesToEnsure) {
            if (!usersToCreate.some(u => u.role === role)) {
                usersToCreate.push({
                    email: `${role.toLowerCase().replace(/\s+/g, '')}@example.com`,
                    password: hashedPassword,
                    full_name: `${role.charAt(0).toUpperCase() + role.slice(1)} User`,
                    phone_number: faker.phone.number(),
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
        const memberUsers = createdUsers.filter(u => u.role === "member" || u.role === "guest");

        const defaultOrganizer = organizerUsers.length > 0 ? organizerUsers[0] : (adminUsers.length > 0 ? adminUsers[0] : createdUsers[0]);
        const defaultScannerOrUploader = adminUsers.length > 0 ? adminUsers[0] : defaultOrganizer;
        const defaultFinanceConfirmer = financeUsers.length > 0 ? financeUsers[0] : defaultScannerOrUploader;


        // --- 3. Seed Events ---
        console.log("Seeding events...");
        const eventsData = [];
        for (let i = 0; i < 10; i++) { // Create 10 events
            const deadlineDays = faker.number.int({ min: 7, max: 90 }); // Registration deadline in 7 to 90 days
            eventsData.push({
                name: faker.company.catchPhrase() + " " + faker.helpers.arrayElement(["Summit", "Workshop", "Conference", "Fest"]),
                location: faker.location.city() + ", " + faker.location.streetAddress(),
                poster_url: faker.image.urlLoremFlickr({ category: 'event,conference,technology' }),
                max_participants: faker.number.int({ min: 50, max: 500 }),
                organizer: defaultOrganizer._id,
                faculty: faker.helpers.arrayElement(createdFaculties)._id,
                registration_deadline: faker.date.soon({ days: deadlineDays }),
                // created_at will default
            });
        }
        const createdEvents = await Event.insertMany(eventsData);
        console.log(`${createdEvents.length} events seeded.`);

        // --- 4. Seed Event Details ---
        console.log("Seeding event details...");
        const eventDetailsData = [];
        for (const event of createdEvents) {
            const numberOfDetails = faker.number.int({ min: 1, max: 4 });
            for (let i = 0; i < numberOfDetails; i++) {
                let sessionDate = faker.date.between({
                    from: event.created_at || new Date(new Date().setDate(new Date().getDate() - 5)), // 5 days before now (or event creation)
                    to: event.registration_deadline // Session must be on or before deadline
                });
                // Ensure 'from' is before 'to' if event.created_at is very close to registration_deadline
                if (sessionDate > event.registration_deadline) {
                    sessionDate = event.registration_deadline;
                }
                // Additional check to ensure 'from' is strictly before 'to' for faker.date.between
                let fromDateForSession = event.created_at || new Date(new Date().setDate(new Date().getDate() - 5));
                if (fromDateForSession >= event.registration_deadline) {
                    fromDateForSession = new Date(new Date(event.registration_deadline).setDate(event.registration_deadline.getDate() - 1)); // Ensure 'from' is at least one day before 'to'
                }
                if (fromDateForSession < event.registration_deadline) {
                    sessionDate = faker.date.between({
                        from: fromDateForSession,
                        to: event.registration_deadline
                    });
                } else {
                    sessionDate = event.registration_deadline; // Fallback if dates are problematic
                }


                const startHour = faker.number.int({ min: 8, max: 16 });
                const endHour = startHour + faker.number.int({ min: 1, max: 3 });

                eventDetailsData.push({
                    event_id: event._id,
                    session_title: faker.lorem.sentence(5) + (i === 0 ? " (Keynote)" : ` (Sesi ${i + 1})`),
                    session_date: sessionDate,
                    start_time: `${String(startHour).padStart(2, '0')}:00`,
                    end_time: `${String(endHour > 23 ? 23 : endHour).padStart(2, '0')}:00`, // Cap at 23:00
                    location: "Ruang " + faker.string.alphanumeric(3).toUpperCase() + " / " + faker.location.secondaryAddress(),
                    speaker: faker.person.fullName(),
                    description: faker.lorem.paragraphs(1),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 0, max: 200000, dec: 0 })), // Adjusted price range
                    // created_at will default
                });
            }
        }
        const createdEventDetails = await EventDetail.insertMany(eventDetailsData);
        console.log(`${createdEventDetails.length} event details seeded.`);

        // --- 5. Seed Event Packages ---
        console.log("Seeding event packages...");
        const eventPackagesData = [];
        for (const event of createdEvents) {
            const numberOfPackages = faker.number.int({ min: 1, max: 3 });
            for (let i = 0; i < numberOfPackages; i++) {
                eventPackagesData.push({
                    event_id: event._id,
                    package_name: faker.helpers.arrayElement(["Tiket Dasar", "Akses Penuh", "Pengalaman VIP"]) + (i > 0 ? ` ${i + 1}` : ''),
                    price: mongoose.Types.Decimal128.fromString(faker.commerce.price({ min: 50000, max: 500000, dec: 0 })), // Adjusted price range
                    description: faker.lorem.sentence(),
                    // created_at will default
                });
            }
        }
        const createdEventPackages = await EventPackage.insertMany(eventPackagesData);
        console.log(`${createdEventPackages.length} event packages seeded.`);


        // --- 6. Seed Registrations ---
        console.log("Seeding registrations...");
        const registrationsData = [];
        const availableUsersForReg = memberUsers.length > 0 ? memberUsers : createdUsers;

        for (let i = 0; i < 60 && availableUsersForReg.length > 0 && createdEvents.length > 0; i++) {
            const user = faker.helpers.arrayElement(availableUsersForReg);
            const event = faker.helpers.arrayElement(createdEvents);

            let detailId = null;
            let packageId = null;
            // let registrationPrice = 0; // This variable was not used

            // Randomly choose to register for a detail or a package
            if (faker.datatype.boolean()) { // Register for a detail
                const detailsForThisEvent = createdEventDetails.filter(ed => ed.event_id.equals(event._id));
                if (detailsForThisEvent.length > 0) {
                    const selectedDetail = faker.helpers.arrayElement(detailsForThisEvent);
                    detailId = selectedDetail._id;
                    // registrationPrice = parseFloat(selectedDetail.price.toString());
                }
            } else { // Register for a package
                const packagesForThisEvent = createdEventPackages.filter(ep => ep.event_id.equals(event._id));
                if (packagesForThisEvent.length > 0) {
                    const selectedPackage = faker.helpers.arrayElement(packagesForThisEvent);
                    packageId = selectedPackage._id;
                    // registrationPrice = parseFloat(selectedPackage.price.toString());
                }
            }

            if (detailId || packageId) { // Only create registration if a detail or package was selected
                registrationsData.push({
                    user_id: user._id,
                    event_id: event._id,
                    detail_id: detailId,
                    package_id: packageId,
                    payment_status: faker.helpers.arrayElement(['pending', 'confirmed', 'rejected']),
                    // registration_date will default via schema
                });
            }
        }
        const createdRegistrations = await Registration.insertMany(registrationsData);
        console.log(`${createdRegistrations.length} registrations seeded.`);

        // --- 7. Seed Payments ---
        console.log("Seeding payments...");
        const paymentsData = [];
        for (const reg of createdRegistrations) {
            if (reg.payment_status !== 'pending' || faker.datatype.boolean(0.5)) { // Create payment for confirmed/rejected, or 50% of pending
                let amount = mongoose.Types.Decimal128.fromString("0.00");
                if (reg.detail_id) {
                    const detail = createdEventDetails.find(d => d._id.equals(reg.detail_id));
                    if (detail) amount = detail.price;
                } else if (reg.package_id) {
                    const pkg = createdEventPackages.find(p => p._id.equals(reg.package_id));
                    if (pkg) amount = pkg.price;
                }

                if (parseFloat(amount.toString()) > 0 || reg.payment_status !== 'pending') { // Only create payment if there's an amount or status isn't pending
                    let confirmedBy = null;
                    let confirmedAt = null;
                    const paymentCreatedAt = reg.registration_date || new Date(); // Use registration_date or now if null

                    if (reg.payment_status === "confirmed") {
                        confirmedBy = defaultFinanceConfirmer._id;
                        const now = new Date();
                        if (paymentCreatedAt < now) {
                            confirmedAt = faker.date.between({ from: paymentCreatedAt, to: now });
                        } else {
                            // If paymentCreatedAt is in the future or now, set confirmedAt to paymentCreatedAt or now
                            confirmedAt = paymentCreatedAt > now ? now : paymentCreatedAt;
                        }
                    }

                    paymentsData.push({
                        registration_id: reg._id,
                        proof_url: faker.image.urlLoremFlickr({ category: 'receipt,payment' }),
                        amount: amount,
                        status: reg.payment_status, // Match registration's payment status
                        confirmed_by: confirmedBy,
                        confirmed_at: confirmedAt,
                        // created_at will default via schema
                    });
                }
            }
        }
        const createdPayments = await Payment.insertMany(paymentsData);
        console.log(`${createdPayments.length} payments seeded.`);

        // --- 8. Seed Attendances ---
        console.log("Seeding attendances...");
        const attendancesData = [];
        for (const reg of createdRegistrations) {
            // Attend if registration is confirmed and has a specific detail
            if (reg.payment_status === 'confirmed' && reg.detail_id) {
                const eventDetail = createdEventDetails.find(ed => ed._id.equals(reg.detail_id));
                if (eventDetail && faker.datatype.boolean(0.8)) { // 80% chance of confirmed attending the detail
                    // const eventForDetail = createdEvents.find(e => e._id.equals(eventDetail.event_id)); // Not strictly needed for scanTime logic here
                    let scanTime = eventDetail.session_date; // Default to session date

                    // More robust scanTime generation
                    const sessionStartDateTime = new Date(eventDetail.session_date);
                    const [startHourStr] = eventDetail.start_time.split(':');
                    sessionStartDateTime.setHours(parseInt(startHourStr, 10), 0, 0, 0);

                    const sessionEndDateTime = new Date(eventDetail.session_date);
                    const [endHourStr] = eventDetail.end_time.split(':');
                    sessionEndDateTime.setHours(parseInt(endHourStr, 10), 0, 0, 0);

                    if (sessionStartDateTime < sessionEndDateTime) {
                        scanTime = faker.date.between({
                            from: sessionStartDateTime,
                            to: sessionEndDateTime
                        });
                    } else {
                        // Fallback if start/end times are problematic, scan at start of session day
                        scanTime = sessionStartDateTime;
                    }


                    attendancesData.push({
                        registration_id: reg._id,
                        detail_id: reg.detail_id,
                        qr_code: faker.string.uuid(),
                        scanned_by: defaultScannerOrUploader._id,
                        scanned_at: scanTime, // This will use the schema default (Date.now) if not overridden
                    });
                }
            }
        }
        const createdAttendances = await Attendance.insertMany(attendancesData);
        console.log(`${createdAttendances.length} attendances seeded.`);

        // --- 9. Seed Certificates ---
        console.log("Seeding certificates...");
        const certificatesData = [];
        for (const att of createdAttendances) {
            // Create certificate if attended
            certificatesData.push({
                registration_id: att.registration_id,
                detail_id: att.detail_id, // From AttendanceSchema
                certificate_url: faker.internet.url() + `/certs/${att.registration_id}-${att.detail_id}.pdf`,
                uploaded_by: defaultScannerOrUploader._id,
                // uploaded_at will default
            });
        }
        if (certificatesData.length > 0) {
            const createdCertificates = await Certificate.insertMany(certificatesData);
            console.log(`${createdCertificates.length} certificates seeded.`);
        } else {
            console.log("No certificates seeded (likely no attendances).");
        }

        // --- 10. Seed Carts ---
        console.log("Seeding carts...");
        const cartsData = [];
        const usersForCart = memberUsers.length > 0 ? memberUsers.slice(0, 15) : createdUsers.slice(0, 15); // Max 15 users with carts

        for (const user of usersForCart) {
            if (faker.datatype.boolean(0.7) && createdEvents.length > 0) { // 70% of these users have a cart
                const event = faker.helpers.arrayElement(createdEvents);
                let detailId = null;
                let packageId = null;

                if (faker.datatype.boolean()) {
                    const detailsForThisEvent = createdEventDetails.filter(ed => ed.event_id.equals(event._id));
                    if (detailsForThisEvent.length > 0) {
                        detailId = faker.helpers.arrayElement(detailsForThisEvent)._id;
                    }
                } else {
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
                        // added_at will default
                    });
                }
            }
        }
        if (cartsData.length > 0) {
            const createdCarts = await Cart.insertMany(cartsData);
            console.log(`${createdCarts.length} carts seeded.`);
        } else {
            console.log("No carts seeded.");
        }

        console.log("Database seeding completed successfully!");

    } catch (err) {
        console.error("Seeding failed catastrophically:", err);
    } finally {
        await mongoose.disconnect();
        console.log("Database disconnected.");
    }
};

seedDatabase();
