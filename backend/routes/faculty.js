import express from "express";
import Faculty from "../model/Faculty.js";

const router = express.Router();

router.get('/code/:code', async (req, res) => {
    try {
        const { code } = req.params;
        console.log(code);
        const faculty = await Faculty.findOne({ code });
        if (!faculty) {
            return res.status(404).json({ success: false, message: 'Faculty not found' });
        }
        res.status(200).json({ success: true, data: faculty });
    } catch (error) {
        console.error('Error fetching faculty:', error);
        res.status(500).json({ success: false, message: 'Server error while fetching faculty' });
    }
});

export default router;