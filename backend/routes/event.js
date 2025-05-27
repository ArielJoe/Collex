import express from "express";
import Event from "../model/Event.js";

const router = express.Router();

// Get all events with filtering and pagination
router.get('/', async (req, res) => {
    const { page = 1, limit = 10, organizer_id } = req.query;

    try {
        // Build query filter
        let filter = {};
        if (organizer_id) {
            filter.organizer_id = organizer_id;
        }

        // Execute query with pagination
        const events = await Event.find(filter)
            .select('name start_time end_time location speaker poster_url registration_fee max_participants organizer_id created_at')
            .limit(limit * 1)
            .skip((page - 1) * limit)
            .sort({ created_at: -1 })
            .exec();

        // Get total count for pagination
        const totalCount = await Event.countDocuments(filter);

        res.json({
            events,
            totalPages: Math.ceil(totalCount / limit),
            currentPage: page * 1,
            totalEvents: totalCount
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while fetching events' });
    }
});

// Get single event by ID
router.get('/:id', async (req, res) => {
    try {
        const event = await Event.findById(req.params.id);
        if (!event) {
            return res.status(404).json({ message: 'Event not found' });
        }
        res.json(event);
    } catch (error) {
        res.status(500).json({ message: 'Server error', error });
    }
});

// Create new event
router.post('/', async (req, res) => {
    try {
        const {
            name,
            description,
            start_time,
            end_time,
            location,
            speaker,
            poster_url,
            registration_fee,
            max_participants,
            organizer_id
        } = req.body;

        // Validation
        if (!name || !start_time || !end_time || !location || !organizer_id) {
            return res.status(400).json({
                message: 'Missing required fields: name, start_time, end_time, location, organizer_id'
            });
        }

        // Check if end_time is after start_time
        if (new Date(end_time) <= new Date(start_time)) {
            return res.status(400).json({
                message: 'End time must be after start time'
            });
        }

        const newEvent = new Event({
            name,
            description,
            start_time: new Date(start_time),
            end_time: new Date(end_time),
            location,
            speaker,
            poster_url,
            registration_fee: registration_fee || 0,
            max_participants: max_participants || 100,
            organizer_id,
            created_at: new Date(),
            updated_at: new Date()
        });

        const savedEvent = await newEvent.save();
        res.status(201).json({
            message: 'Event created successfully',
            event: savedEvent
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while creating event', error: error.message });
    }
});

// Update event
router.put('/:id', async (req, res) => {
    try {
        const eventId = req.params.id;
        const updateData = req.body;

        // Remove fields that shouldn't be updated directly
        delete updateData._id;
        delete updateData.created_at;

        // Add updated_at timestamp
        updateData.updated_at = new Date();

        // Validate time if provided
        if (updateData.start_time && updateData.end_time) {
            if (new Date(updateData.end_time) <= new Date(updateData.start_time)) {
                return res.status(400).json({
                    message: 'End time must be after start time'
                });
            }
        }

        const updatedEvent = await Event.findByIdAndUpdate(
            eventId,
            updateData,
            { new: true, runValidators: true }
        );

        if (!updatedEvent) {
            return res.status(404).json({ message: 'Event not found' });
        }

        res.json({
            message: 'Event updated successfully',
            event: updatedEvent
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while updating event', error: error.message });
    }
});

// Delete event
router.delete('/:id', async (req, res) => {
    try {
        const eventId = req.params.id;

        const deletedEvent = await Event.findByIdAndDelete(eventId);

        if (!deletedEvent) {
            return res.status(404).json({ message: 'Event not found' });
        }

        res.json({
            message: 'Event deleted successfully',
            event: deletedEvent
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while deleting event', error: error.message });
    }
});

// Get events by organizer (for organizer dashboard)
router.get('/organizer/:organizer_id', async (req, res) => {
    const { page = 1, limit = 10 } = req.query;
    const organizer_id = req.params.organizer_id;

    try {
        const events = await Event.find({ organizer_id })
            .select('name start_time end_time location speaker registration_fee max_participants created_at')
            .limit(limit * 1)
            .skip((page - 1) * limit)
            .sort({ created_at: -1 })
            .exec();

        const totalCount = await Event.countDocuments({ organizer_id });

        res.json({
            events,
            totalPages: Math.ceil(totalCount / limit),
            currentPage: page * 1,
            totalEvents: totalCount
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while fetching organizer events' });
    }
});

export default router;
