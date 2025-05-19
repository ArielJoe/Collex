import express from 'express';
import User from '../model/User.js';
import bcrypt from 'bcrypt';

const router = express.Router();

// Get user stats
router.get('/stats', async (req, res) => {
    try {
        const stats = {
            member: await User.countDocuments({ role: 'member', is_active: true }),
            finance: await User.countDocuments({ role: 'finance', is_active: true }),
            organizer: await User.countDocuments({ role: 'organizer', is_active: true }),
        };
        res.json(stats);
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

// List users with role filter and pagination
router.get('/', async (req, res) => {
    const { role, page = 1, limit = 10 } = req.query;
    const query = { is_active: true };
    if (role && ['member', 'finance', 'organizer'].includes(role)) {
        query.role = role;
    }
    try {
        const users = await User.find(query)
            .select('full_name email phone_number role created_at')
            .limit(limit * 1)
            .skip((page - 1) * limit)
            .exec();
        const count = await User.countDocuments(query);
        res.json({
            users,
            totalPages: Math.ceil(count / limit),
            currentPage: page * 1,
        });
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

// Get single user
router.get('/:id', async (req, res) => {
    try {
        const user = await User.findOne({ _id: req.params.id }).select('full_name email phone_number role');
        // console.log(user);
        if (user.is_active) {
            return res.status(404).json({ message: 'User not found' });
        }
        res.json(user);
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

// Create user
router.post('/', async (req, res) => {
    const { full_name, phone_number, email, password, role } = req.body;
    if (!full_name || !phone_number || !email || !password || !role) {
        return res.status(400).json({ message: 'All fields are required' });
    }
    if (!['member', 'finance', 'organizer'].includes(role)) {
        return res.status(400).json({ message: 'Invalid role' });
    }
    if (!/^\d{10,12}$/.test(phone_number)) {
        return res.status(400).json({ message: 'Phone number must be 10-12 digits' });
    }
    try {
        const existingUser = await User.findOne({ $or: [{ email }, { phone_number }] });
        if (existingUser) {
            return res.status(400).json({ message: 'Email or phone number already exists' });
        }
        const hashedPassword = await bcrypt.hash(password, 10);
        const user = new User({
            full_name,
            phone_number,
            email,
            password: hashedPassword,
            role,
        });
        await user.save();
        res.status(201).json({ message: 'User created successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

// Update user
router.put('/:id', async (req, res) => {
    const { full_name, phone_number, email, password, role } = req.body;
    if (!full_name || !phone_number || !email || !role) {
        return res.status(400).json({ message: 'Required fields missing' });
    }
    if (!['member', 'finance', 'organizer'].includes(role)) {
        return res.status(400).json({ message: 'Invalid role' });
    }
    if (!/^\d{10,12}$/.test(phone_number)) {
        return res.status(400).json({ message: 'Phone number must be 10-12 digits' });
    }
    try {
        const user = await User.findById(req.params.id);
        if (!user || !user.is_active) {
            return res.status(404).json({ message: 'User not found' });
        }
        const existingUser = await User.findOne({
            $or: [{ email }, { phone_number }],
            _id: { $ne: req.params.id },
        });
        if (existingUser) {
            return res.status(400).json({ message: 'Email or phone number already exists' });
        }
        user.full_name = full_name;
        user.phone_number = phone_number;
        user.email = email;
        user.role = role;
        if (password) {
            user.password = await bcrypt.hash(password, 10);
        }
        await user.save();
        res.json({ message: 'User updated successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

// Delete user (soft delete)
router.delete('/:id', async (req, res) => {
    try {
        const user = await User.findById(req.params.id);
        if (!user || !user.is_active) {
            return res.status(404).json({ message: 'User not found' });
        }
        user.is_active = false;
        await user.save();
        res.json({ message: 'User deleted successfully' });
    } catch (error) {
        res.status(500).json({ message: 'Server error' });
    }
});

export default router;
