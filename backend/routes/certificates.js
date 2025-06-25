import express from 'express';
import Certificate from '../model/Certificate.js';
import Registration from '../model/Registration.js';

const router = express.Router();

// GET /api/certificates/organizer/:organizerId
router.get('/organizer/:organizerId', async (req, res) => {
    try {
        const { organizerId } = req.params;

        // Ambil semua registrasi yang event.detail-nya dimiliki oleh organizer
        const registrations = await Registration.find()
            .populate({
                path: 'detail_id',
                populate: {
                    path: 'event_id',
                    match: { organizer: organizerId }, // Filter berdasarkan organizer
                    select: '_id name'
                }
            });

        // Filter hanya registrasi yang event-nya memang milik organizer ini
        const filteredRegistrations = registrations.filter(r => r.detail_id?.event_id);

        const registrationIds = filteredRegistrations.map(r => r._id);

        const certificates = await Certificate.find({ registration_id: { $in: registrationIds } })
            .populate({
                path: 'detail_id',
                select: 'title start_time end_time'
            })
            .populate({
                path: 'registration_id',
                populate: {
                    path: 'user_id',
                    select: 'fullname email'
                }
            })
            .select('certificate_url uploaded_at');

        res.status(200).json({ success: true, data: certificates });
    } catch (error) {
        console.error('Error fetching organizer certificates:', error);
        res.status(500).json({ success: false, message: 'Internal server error.' });
    }
});

// GET /api/certificates/user/:userId
router.get('/user/:userId', async (req, res) => {
    try {
        const { userId } = req.params;

        const registrationIds = await Registration.find({ user_id: userId }).select('_id');
        const ids = registrationIds.map(r => r._id);

        const certificates = await Certificate.find({ registration_id: { $in: ids } })
            .populate('detail_id', 'title start_time end_time')
            .select('certificate_url uploaded_at');

        if (!certificates.length) {
            return res.status(404).json({ success: false, message: 'No certificates found for this user.' });
        }

        res.status(200).json({ success: true, data: certificates });
    } catch (error) {
        console.error('Error fetching user certificates:', error);
        res.status(500).json({ success: false, message: 'Internal server error.' });
    }
});

// POST /api/certificates (upload sertifikat)
router.post('/', async (req, res) => {
    try {
        const { registration_id, detail_id, certificate_url, uploaded_by } = req.body;

        if (!registration_id || !detail_id || !certificate_url || !uploaded_by) {
            return res.status(400).json({ success: false, message: 'Missing required fields.' });
        }

        const newCertificate = new Certificate({
            registration_id,
            detail_id,
            certificate_url,
            uploaded_by,
            uploaded_at: new Date()
        });

        await newCertificate.save();

        res.status(201).json({ success: true, message: 'Certificate uploaded successfully.', data: newCertificate });
    } catch (error) {
        console.error('Error uploading certificate:', error);
        res.status(500).json({ success: false, message: 'Failed to upload certificate.' });
    }
});

// GET /api/certificates/eligible/:organizerId
router.get('/eligible/:organizerId', async (req, res) => {
    try {
        const { organizerId } = req.params;
        const now = new Date();

        const registrations = await Registration.find()
            .populate({
                path: 'detail_id',
                match: { end_time: { $lt: now } }, // hanya event yang sudah selesai
                populate: {
                    path: 'event_id',
                    match: { organizer: organizerId }, // event milik organizer ini
                    select: '_id name'
                }
            })
            .populate({
                path: 'user_id',
                select: 'full_name'
            });

        // Filter hanya registrasi yang punya detail dan event valid
        const eligible = registrations.filter(r => r.detail_id?.event_id);

        const result = eligible.map(r => ({
            _id: r._id,
            user: r.user_id,
            event: r.detail_id.event_id,
            detail: r.detail_id
        }));

        res.status(200).json({ success: true, data: result });
    } catch (error) {
        console.error('Error fetching eligible registrations:', error);
        res.status(500).json({ success: false, message: 'Internal server error.' });
    }
});

export default router;
