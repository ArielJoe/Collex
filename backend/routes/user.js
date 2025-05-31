import express from 'express';
import User from '../model/User.js'; // Path ke model User
import bcrypt from 'bcryptjs'; // bcryptjs sudah diimpor, bukan bcrypt

const router = express.Router();

// Get user stats
router.get('/stats', async (req, res) => {
    try {
        const stats = {
            member: await User.countDocuments({ role: 'member', is_active: true }),
            finance: await User.countDocuments({ role: 'finance', is_active: true }),
            organizer: await User.countDocuments({ role: 'organizer', is_active: true }),
            admin: await User.countDocuments({ role: 'admin', is_active: true }), // Menambahkan admin jika relevan
        };
        res.status(200).json({ success: true, data: stats });
    } catch (error) {
        console.error("Error fetching user stats:", error);
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

// List users with role filter and pagination
router.get('/', async (req, res) => {
    const { role, page = 1, limit = 10, search } = req.query;
    const query = { is_active: true }; // Hanya mengambil user yang aktif secara default

    if (role && ['member', 'finance', 'organizer', 'admin'].includes(role)) {
        query.role = role;
    }

    if (search) {
        query.$or = [
            { full_name: { $regex: search, $options: 'i' } },
            { email: { $regex: search, $options: 'i' } }
        ];
    }

    try {
        const users = await User.find(query)
            .select('full_name email phone_number role photo_url is_active') // Menambahkan photo_url dan is_active
            .limit(parseInt(limit))
            .skip((parseInt(page) - 1) * parseInt(limit))
            .sort({ full_name: 1 }) // Mengurutkan berdasarkan nama
            .exec();

        const count = await User.countDocuments(query);

        res.status(200).json({
            success: true,
            data: users,
            totalPages: Math.ceil(count / parseInt(limit)),
            currentPage: parseInt(page),
            totalUsers: count
        });
    } catch (error) {
        console.error("Error fetching users:", error);
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

// Get single user by ID
router.get('/:id', async (req, res) => {
    try {
        const user = await User.findById(req.params.id).select('-password'); // Jangan kirim password

        if (!user) { // Cek apakah user ada, bukan user.is_active
            return res.status(404).json({ success: false, message: 'User not found' });
        }
        if (!user.is_active) {
            console.log(`User ${req.params.id} found but is inactive.`);
            // Anda bisa memilih untuk tetap mengirim data user yang tidak aktif atau error 404/403
            // return res.status(403).json({ success: false, message: 'User account is inactive' });
        }
        res.status(200).json({ success: true, data: user });
    } catch (error) {
        console.error(`Error fetching user ${req.params.id}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'User not found (invalid ID format)' });
        }
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

// Create user (biasanya registrasi ditangani oleh /auth/register, ini mungkin untuk admin)
router.post('/', async (req, res) => {
    const { full_name, phone_number, email, password, role, photo_url } = req.body;

    if (!full_name || !phone_number || !email || !password || !role) {
        return res.status(400).json({ success: false, message: 'Full name, phone number, email, password, and role are required' });
    }

    const allowedRoles = ['member', 'admin', 'finance', 'organizer'];
    if (!allowedRoles.includes(role)) {
        return res.status(400).json({ success: false, message: `Invalid role. Allowed roles: ${allowedRoles.join(', ')}` });
    }
    // Validasi nomor telepon bisa lebih spesifik jika diperlukan
    // if (!/^\d{10,15}$/.test(phone_number)) {
    //     return res.status(400).json({ success: false, message: 'Phone number must be 10-15 digits' });
    // }

    try {
        let user = await User.findOne({ email });
        if (user) {
            return res.status(400).json({ success: false, message: 'Email already exists' });
        }
        // Bisa juga cek phone_number jika harus unik

        const salt = await bcrypt.genSalt(10);
        const hashedPassword = await bcrypt.hash(password, salt);

        user = new User({
            full_name,
            phone_number,
            email,
            password: hashedPassword,
            role,
            photo_url: photo_url || null,
            is_active: true // Default is_active
        });

        await user.save();
        const userResponse = { ...user.toObject() };
        delete userResponse.password; // Jangan kirim password kembali

        res.status(201).json({ success: true, message: 'User created successfully', data: userResponse });
    } catch (error) {
        console.error("Error creating user:", error);
        if (error.code === 11000) {
            return res.status(400).json({ success: false, message: "Email or other unique field already exists." });
        }
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

// Update user by ID
router.put('/:id', async (req, res) => {
    const { full_name, phone_number, email, role, is_active, photo_url, password } = req.body;

    try {
        let user = await User.findById(req.params.id);
        if (!user) {
            return res.status(404).json({ success: false, message: 'User not found' });
        }

        // Validasi jika email diubah dan sudah ada
        if (email && email !== user.email) {
            const existingUserWithEmail = await User.findOne({ email });
            if (existingUserWithEmail && existingUserWithEmail._id.toString() !== req.params.id) {
                return res.status(400).json({ success: false, message: 'Email already in use by another account' });
            }
            user.email = email;
        }

        if (full_name) user.full_name = full_name;
        if (phone_number) user.phone_number = phone_number;
        if (photo_url) user.photo_url = photo_url;

        if (role) {
            const allowedRoles = ['member', 'admin', 'finance', 'organizer'];
            if (!allowedRoles.includes(role)) {
                return res.status(400).json({ success: false, message: `Invalid role. Allowed roles: ${allowedRoles.join(', ')}` });
            }
            user.role = role;
        }

        if (typeof is_active === 'boolean') {
            user.is_active = is_active;
        }

        if (password) {
            const salt = await bcrypt.genSalt(10);
            user.password = await bcrypt.hash(password, salt);
        }

        await user.save();
        const userResponse = { ...user.toObject() };
        delete userResponse.password;

        res.status(200).json({ success: true, message: 'User updated successfully', data: userResponse });
    } catch (error) {
        console.error(`Error updating user ${req.params.id}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'User not found (invalid ID format)' });
        }
        if (error.code === 11000) {
            return res.status(400).json({ success: false, message: "Email or other unique field conflict." });
        }
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

// Delete user (soft delete by setting is_active to false)
router.delete('/:id', async (req, res) => {
    try {
        const user = await User.findById(req.params.id);
        if (!user) {
            return res.status(404).json({ success: false, message: 'User not found' });
        }

        if (!user.is_active) {
            return res.status(400).json({ success: false, message: 'User is already inactive' });
        }

        user.is_active = false;
        await user.save();

        res.status(200).json({ success: true, message: 'User deactivated successfully (soft delete)' });
    } catch (error) {
        console.error(`Error deactivating user ${req.params.id}:`, error);
        if (error.kind === 'ObjectId') {
            return res.status(404).json({ success: false, message: 'User not found (invalid ID format)' });
        }
        res.status(500).json({ success: false, message: 'Server error' });
    }
});

export default router;
