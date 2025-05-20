import express from "express";
import Event from "../model/Event.js";

const router = express.Router();

// Get all events with filtering and pagination
router.get('/', async (req, res) => {
    const { page = 1, limit = 10 } = req.query;

    try {
        // Execute query with pagination
        const events = await Event.find()
            .select('name date_time location speaker poster_url registration_fee max_participants organizer_id created_at')
            .limit(limit * 1)
            .skip((page - 1) * limit)
            .exec();
        // console.log(events);
        // Get total count for pagination
        const count = events.length;

        res.json({
            events,
            totalPages: Math.ceil(count / limit),
            currentPage: page * 1,
            totalEvents: count
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ message: 'Server error while fetching events' });
    }
});

export default router;
