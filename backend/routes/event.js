import mongoose from "mongoose";
import express from "express";
import Event from "../model/Event.js"; // Path ke model Event
import User from "../model/User.js"; // Untuk validasi organizer
import Faculty from "../model/Faculty.js"; // Untuk validasi faculty
import EventDetail from "../model/EventDetail.js";

const router = express.Router();

// Get all events with filtering, pagination, and search
router.get('/', async (req, res) => {
    const { page = 1, limit = 10, faculty_id, organizer_id, search, upcoming, past } = req.query;

    try {
        let filter = {};
        if (faculty_id) {
            filter.faculty = faculty_id; // Sesuai schema: 'faculty' bukan 'faculty_id'
        }
        if (organizer_id) {
            filter.organizer = organizer_id; // Sesuai schema: 'organizer' bukan 'organizer_id'
        }
        if (search) {
            filter.name = { $regex: search, $options: 'i' }; // Cari berdasarkan nama event
        }

        const now = new Date();
        if (upcoming === 'true') {
            filter.registration_deadline = { $gte: now }; // Event yang pendaftarannya masih buka atau akan datang
        }
        if (past === 'true') {
            filter.registration_deadline = { $lt: now }; // Event yang pendaftarannya sudah lewat
        }


        const events = await Event.find(filter)
            // Populate organizer dan faculty untuk mendapatkan detail, bukan hanya ID
            .populate('organizer', 'full_name email') // Pilih field yang ingin ditampilkan dari User
            .populate('faculty', 'name code')       // Pilih field yang ingin ditampilkan dari Faculty
            .limit(parseInt(limit))
            .skip((parseInt(page) - 1) * parseInt(limit))
            .sort({ created_at: -1 }) // Urutkan berdasarkan tanggal pembuatan event
            .exec();

        const totalCount = await Event.countDocuments(filter);

        res.status(200).json({
            success: true,
            data: events,
            totalPages: Math.ceil(totalCount / parseInt(limit)),
            currentPage: parseInt(page),
            totalEvents: totalCount
        });
    } catch (error) {
        console.error("Error fetching events:", error);
        res.status(500).json({ success: false, message: 'Server error while fetching events' });
    }
});

// Get event by ID with its details
router.get('/:id', async (req, res) => {
    try {
        const eventId = req.params.id;

        // Validate event ID
        if (!mongoose.Types.ObjectId.isValid(eventId)) {
            return res.status(400).json({ success: false, message: 'Invalid event ID' });
        }

        // Get the main event information
        const event = await Event.findById(eventId)
            .populate('organizer', 'name email')
            .populate('faculty', 'name');

        if (!event) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        // Convert Decimal128 to string for better handling in frontend
        const eventObj = event.toObject();
        if (eventObj.registered_participant) {
            eventObj.registered_participant = eventObj.registered_participant.toString();
        }

        // Get all details associated with this event
        const eventDetails = await EventDetail.find({ event_id: eventId });

        // Convert Decimal128 prices to string
        const formattedDetails = eventDetails.map(detail => {
            const detailObj = detail.toObject();
            if (detailObj.price) {
                detailObj.price = detailObj.price.toString();
            }
            return detailObj;
        });

        res.json({
            success: true,
            data: {
                ...eventObj,
                details: formattedDetails
            }
        });
    } catch (error) {
        console.error('Error fetching event:', error);
        res.status(500).json({
            success: false,
            message: 'Server error',
            error: error.message
        });
    }
});

// Create new event
router.post('/', async (req, res) => {
    try {
        const {
            name,
            location,
            poster_url,
            max_participant,
            start_time,
            end_time,
            organizer, // ID User
            faculty,   // ID Faculty
            registration_deadline
        } = req.body;

        // Validasi input dasar
        if (!name || !location || !max_participant || !organizer || !faculty || !registration_deadline) {
            return res.status(400).json({
                success: false,
                message: 'Missing required fields: name, location, max_participant, organizer, faculty, registration_deadline'
            });
        }

        // Validasi apakah organizer (User) ada
        const organizerExists = await User.findById(organizer);
        if (!organizerExists) {
            return res.status(400).json({ success: false, message: 'Invalid organizer ID. User not found.' });
        }
        // Bisa juga cek role organizer jika perlu: if (organizerExists.role !== 'organizer') ...

        // Validasi apakah faculty (Faculty) ada
        const facultyExists = await Faculty.findById(faculty);
        if (!facultyExists) {
            return res.status(400).json({ success: false, message: 'Invalid faculty ID. Faculty not found.' });
        }

        // Validasi tanggal registration_deadline
        if (new Date(registration_deadline) <= new Date()) {
            return res.status(400).json({
                success: false,
                message: 'Registration deadline must be a future date'
            });
        }

        const newEvent = new Event({
            name,
            location,
            poster_url: poster_url || null,
            max_participant: parseInt(max_participant),
            organizer, // Simpan ID
            faculty,   // Simpan ID
            start_time,
            end_time,
            registration_deadline: new Date(registration_deadline),
            // created_at akan default dari schema
        });

        const savedEvent = await newEvent.save();
        // Populate setelah menyimpan untuk respons yang lebih kaya
        const populatedEvent = await Event.findById(savedEvent._id)
            .populate('organizer', 'full_name email')
            .populate('faculty', 'name code');

        res.status(201).json({
            success: true,
            message: 'Event created successfully',
            data: populatedEvent
        });
    } catch (error) {
        console.error("Error creating event:", error);
        if (error.name === 'ValidationError') {
            return res.status(400).json({ success: false, message: error.message });
        }
        res.status(500).json({ success: false, message: 'Server error while creating event' });
    }
});

router.post('/details', async (req, res) => {
    try {
        const { event_id, title, start_time, end_time, location, speaker, description, price } = req.body;

        // Validate input
        if (!event_id || !mongoose.Types.ObjectId.isValid(event_id)) {
            return res.status(400).json({ success: false, message: 'Invalid event ID' });
        }
        if (!title || !start_time || !end_time || !location || !speaker || !description) {
            return res.status(400).json({ success: false, message: 'All fields except price are required' });
        }

        const eventExists = await Event.findById(event_id);
        if (!eventExists) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        const newDetail = new EventDetail({
            event_id,
            title,
            start_time: new Date(start_time),
            end_time: new Date(end_time),
            location,
            speaker,
            description,
            price: price ? mongoose.Types.Decimal128.fromString(price.toString()) : null,
        });

        const savedDetail = await newDetail.save();

        res.status(201).json({
            success: true,
            message: 'Event detail created successfully',
            data: savedDetail
        });
    } catch (error) {
        console.error('Error creating event detail:', error);
        if (error.name === 'ValidationError') {
            return res.status(400).json({ success: false, message: error.message });
        }
        res.status(500).json({ success: false, message: 'Server error while creating event detail' });
    }
});

// Update event by ID
router.put('/:id', async (req, res) => {
    try {
        const eventId = req.params.id;
        const updateData = req.body;

        // Hapus field yang tidak seharusnya diupdate langsung atau dikelola secara otomatis
        delete updateData._id;
        delete updateData.created_at; // created_at tidak diupdate
        // organizer dan faculty bisa diupdate jika diperlukan, pastikan validasi ID baru

        if (updateData.organizer) {
            const organizerExists = await User.findById(updateData.organizer);
            if (!organizerExists) {
                return res.status(400).json({ success: false, message: 'Invalid new organizer ID. User not found.' });
            }
        }
        if (updateData.faculty) {
            const facultyExists = await Faculty.findById(updateData.faculty);
            if (!facultyExists) {
                return res.status(400).json({ success: false, message: 'Invalid new faculty ID. Faculty not found.' });
            }
        }
        if (updateData.registration_deadline && new Date(updateData.registration_deadline) <= new Date()) {
            return res.status(400).json({
                success: false,
                message: 'Registration deadline must be a future date'
            });
        }
        if (updateData.max_participant) {
            updateData.max_participant
                = parseInt(updateData.max_participant);
        }


        const updatedEvent = await Event.findByIdAndUpdate(
            eventId,
            { $set: updateData }, // Gunakan $set untuk update parsial yang aman
            { new: true, runValidators: true }
        )
            .populate('organizer', 'full_name email')
            .populate('faculty', 'name code');

        if (!updatedEvent) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        res.status(200).json({
            success: true,
            message: 'Event updated successfully',
            data: updatedEvent
        });
    } catch (error) {
        console.error(`Error updating event ${req.params.id}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'Event not found (invalid ID format)' });
        }
        if (error.name === 'ValidationError') {
            return res.status(400).json({ success: false, message: error.message });
        }
        res.status(500).json({ success: false, message: 'Server error while updating event' });
    }
});

// Delete event by ID
router.delete('/:id', async (req, res) => {
    try {
        const eventId = req.params.id;
        // Pertimbangkan validasi: apakah event ini memiliki registrasi, detail, dll. sebelum menghapus?
        // const relatedRegistrations = await Registration.findOne({ event_id: eventId });
        // if (relatedRegistrations) {
        //     return res.status(400).json({ success: false, message: 'Cannot delete event. It has existing registrations.' });
        // }

        const deletedEvent = await Event.findByIdAndDelete(eventId);

        if (!deletedEvent) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        res.status(200).json({
            success: true,
            message: 'Event deleted successfully',
            data: deletedEvent // Mengembalikan data event yang dihapus
        });
    } catch (error) {
        console.error(`Error deleting event ${req.params.id}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'Event not found (invalid ID format)' });
        }
        res.status(500).json({ success: false, message: 'Server error while deleting event' });
    }
});

// Get events by organizer (sesuai schema baru, organizer adalah ref User)
// Rute ini bisa digabung dengan GET / jika query organizer_id sudah diimplementasikan
router.get('/by-organizer/:organizerId', async (req, res) => {
    const { page = 1, limit = 10 } = req.query;
    const organizerId = req.params.organizerId;

    try {
        const events = await Event.find({ organizer: organizerId }) // Menggunakan 'organizer'
            .populate('faculty', 'name code')
            .limit(parseInt(limit))
            .skip((parseInt(page) - 1) * parseInt(limit))
            .sort({ created_at: -1 })
            .exec();

        const totalCount = await Event.countDocuments({ organizer: organizerId });

        res.status(200).json({
            success: true,
            data: events,
            totalPages: Math.ceil(totalCount / parseInt(limit)),
            currentPage: parseInt(page),
            totalEvents: totalCount
        });
    } catch (error) {
        console.error(`Error fetching events for organizer ${organizerId}:`, error);
        res.status(500).json({ success: false, message: 'Server error while fetching organizer events' });
    }
});

// Get all event details by event ID
router.get('/event-details/by-event/:eventId', async (req, res) => {
    try {
        const eventId = req.params.eventId;

        // Validate event ID
        if (!mongoose.Types.ObjectId.isValid(eventId)) {
            return res.status(400).json({ success: false, message: 'Invalid event ID' });
        }

        // Check if event exists
        const eventExists = await Event.findById(eventId);
        if (!eventExists) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        // Get all event details
        const eventDetails = await EventDetail.find({ event_id: eventId });

        // Convert Decimal128 prices to string
        const formattedDetails = eventDetails.map(detail => {
            const detailObj = detail.toObject();
            if (detailObj.price) {
                detailObj.price = detailObj.price.toString();
            }
            return detailObj;
        });

        res.status(200).json({
            success: true,
            data: formattedDetails,
            totalDetails: formattedDetails.length
        });
    } catch (error) {
        console.error(`Error fetching event details for event ${req.params.eventId}:`, error);
        res.status(500).json({ success: false, message: 'Server error while fetching event details' });
    }
});

// Update event detail by ID
router.put('/details/:detailId', async (req, res) => {
    try {
        const detailId = req.params.detailId;
        const { event_id, title, start_time, end_time, location, speaker, description, price } = req.body;

        // Validate detail ID
        if (!mongoose.Types.ObjectId.isValid(detailId)) {
            return res.status(400).json({ success: false, message: 'Invalid event detail ID' });
        }

        // Validate event ID
        if (!event_id || !mongoose.Types.ObjectId.isValid(event_id)) {
            return res.status(400).json({ success: false, message: 'Invalid event ID' });
        }

        // Validate required fields
        if (!title || !start_time || !end_time || !location || !speaker || !description) {
            return res.status(400).json({ success: false, message: 'All fields except price are required' });
        }

        // Validate date consistency
        if (new Date(end_time) <= new Date(start_time)) {
            return res.status(400).json({ success: false, message: 'End time must be after start time' });
        }

        // Check if event exists
        const eventExists = await Event.findById(event_id);
        if (!eventExists) {
            return res.status(404).json({ success: false, message: 'Event not found' });
        }

        // Check if detail exists
        const detailExists = await EventDetail.findById(detailId);
        if (!detailExists) {
            return res.status(404).json({ success: false, message: 'Event detail not found' });
        }

        // Update event detail
        const updateData = {
            event_id,
            title,
            start_time: new Date(start_time),
            end_time: new Date(end_time),
            location,
            speaker,
            description,
            price: price ? mongoose.Types.Decimal128.fromString(price.toString()) : null,
        };

        const updatedDetail = await EventDetail.findByIdAndUpdate(
            detailId,
            { $set: updateData },
            { new: true, runValidators: true }
        );

        if (!updatedDetail) {
            return res.status(404).json({ success: false, message: 'Event detail not found' });
        }

        // Convert Decimal128 price to string for response
        const detailObj = updatedDetail.toObject();
        if (detailObj.price) {
            detailObj.price = detailObj.price.toString();
        }

        res.status(200).json({
            success: true,
            message: 'Event detail updated successfully',
            data: detailObj
        });
    } catch (error) {
        console.error(`Error updating event detail ${req.params.detailId}:`, error);
        if (error.name === 'ValidationError') {
            return res.status(400).json({ success: false, message: error.message });
        }
        res.status(500).json({ success: false, message: 'Server error while updating event detail' });
    }
});

// Delete event detail by ID
router.delete('/details/:detailId', async (req, res) => {
    try {
        const detailId = req.params.detailId;

        // Validate detail ID
        if (!mongoose.Types.ObjectId.isValid(detailId)) {
            return res.status(400).json({ PolitikanID: detailId });
        }

        // Check if detail exists
        const detail = await EventDetail.findById(detailId);
        if (!detail) {
            return res.status(404).json({ success: false, message: 'Event detail not found' });
        }

        // Delete the event detail
        await EventDetail.findByIdAndDelete(detailId);

        res.status(200).json({
            success: true,
            message: 'Event detail deleted successfully'
        });
    } catch (error) {
        console.error(`Error deleting event detail ${req.params.detailId}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'Event detail not found (invalid ID format)' });
        }
        res.status(500).json({ success: false, message: 'Server error while deleting event detail' });
    }
});

export default router;
