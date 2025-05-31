import express from 'express';
import mongoose from 'mongoose';

// Impor Model
import Cart from '../model/Cart.js'; // Sesuaikan path jika perlu
import Event from '../model/Event.js';
import EventDetail from '../model/EventDetail.js';
import EventPackage from '../model/EventPackage.js';
import User from '../model/User.js'; // Untuk validasi user jika diperlukan

const router = express.Router();

// @desc    Add item to cart
// @route   POST /api/cart/add
// @access  Public (Setelah middleware dihapus)
router.post('/add', async (req, res) => {
    // Ambil user_id dari request body. Dalam produksi, ini seharusnya dari user yang terautentikasi.
    const { event_id, item_id, item_type, user_id } = req.body;

    if (!user_id) {
        return res.status(400).json({ success: false, message: 'User ID is required in the request body.' });
    }

    if (!event_id || !item_id || !item_type) {
        return res.status(400).json({ success: false, message: 'Event ID, Item ID, and Item Type are required.' });
    }
    if (!['detail', 'package'].includes(item_type)) {
        return res.status(400).json({ success: false, message: 'Invalid item_type. Must be "detail" or "package".' });
    }

    try {
        // Validasi apakah user_id valid
        const user = await User.findById(user_id);
        if (!user) {
            return res.status(404).json({ success: false, message: 'User not found.' });
        }

        const event = await Event.findById(event_id);
        if (!event) {
            return res.status(404).json({ success: false, message: 'Event not found.' });
        }

        const query = { user_id, event_id };

        if (item_type === 'detail') {
            const detail = await EventDetail.findById(item_id);
            if (!detail || !detail.event_id.equals(event._id)) {
                return res.status(404).json({ success: false, message: 'Event Detail not found or does not belong to this event.' });
            }
            query.detail_id = item_id;
        } else { // item_type === 'package'
            const pkg = await EventPackage.findById(item_id);
            if (!pkg || !pkg.event_id.equals(event._id)) {
                return res.status(404).json({ success: false, message: 'Event Package not found or does not belong to this event.' });
            }
            query.package_id = item_id;
        }

        const existingCartItem = await Cart.findOne(query);
        if (existingCartItem) {
            return res.status(400).json({ success: false, message: 'Item already in cart.' });
        }

        if (new Date() > new Date(event.registration_deadline)) {
            return res.status(400).json({ success: false, message: 'Registration deadline for this event has passed. Cannot add to cart.' });
        }

        const cartItem = new Cart({
            user_id,
            event_id,
            detail_id: item_type === 'detail' ? item_id : null,
            package_id: item_type === 'package' ? item_id : null,
        });

        await cartItem.save();
        res.status(201).json({ success: true, message: 'Item added to cart successfully.', data: cartItem });

    } catch (error) {
        console.error("Error adding item to cart:", error);
        if (error.kind === 'ObjectId') {
            return res.status(400).json({ success: false, message: 'Invalid ID format for user, event, or item.' });
        }
        res.status(500).json({ success: false, message: 'Server error while adding item to cart.' });
    }
});

// @desc    Get user's cart
// @route   GET /api/cart/:user_id (atau /api/cart?user_id=xxx)
// @access  Public (Setelah middleware dihapus)
router.get('/:user_id', async (req, res) => {
    // Ambil user_id dari parameter rute.
    const { user_id } = req.params;
    // Alternatif: jika dari query string: const { user_id } = req.query; dan rute adalah GET /api/cart

    if (!user_id) {
        return res.status(400).json({ success: false, message: 'User ID is required as a route parameter or query string.' });
    }
    if (!mongoose.Types.ObjectId.isValid(user_id)) {
        return res.status(400).json({ success: false, message: 'Invalid User ID format.' });
    }

    try {
        const cartItems = await Cart.find({ user_id })
            .populate({
                path: 'event_id',
                select: 'name poster_url registration_deadline location start_time'
            })
            .populate({
                path: 'detail_id',
                select: 'title price start_time end_time speaker location'
            })
            .populate({
                path: 'package_id',
                select: 'package_name price description'
            })
            .sort({ added_at: -1 });

        res.status(200).json({ success: true, count: cartItems.length, data: cartItems });
    } catch (error) {
        console.error("Error fetching cart:", error);
        res.status(500).json({ success: false, message: 'Server error while fetching cart.' });
    }
});

// @desc    Remove item from cart
// @route   DELETE /api/cart/item/:cartItemId
// @access  Public (Setelah middleware dihapus)
router.delete('/item/:cartItemId', async (req, res) => {
    // Untuk menghapus item, kita masih memerlukan user_id untuk memastikan item itu milik user yang benar,
    // atau kita bisa menghapusnya langsung jika tidak ada otorisasi.
    // Untuk keamanan minimal, sebaiknya user_id tetap dikirim (misalnya di body atau query).
    const { user_id } = req.body; // Asumsi user_id dikirim di body untuk verifikasi
    const { cartItemId } = req.params;

    if (!user_id) {
        // Jika tidak ingin verifikasi user, baris ini bisa dihilangkan, tapi tidak aman.
        return res.status(400).json({ success: false, message: 'User ID is required in the request body to verify ownership.' });
    }
    if (!mongoose.Types.ObjectId.isValid(cartItemId) || (user_id && !mongoose.Types.ObjectId.isValid(user_id))) {
        return res.status(400).json({ success: false, message: 'Invalid Cart Item ID or User ID format.' });
    }

    try {
        const cartItem = await Cart.findById(cartItemId);

        if (!cartItem) {
            return res.status(404).json({ success: false, message: 'Cart item not found.' });
        }

        // Verifikasi opsional: pastikan item keranjang milik pengguna yang dikirim
        if (user_id && !cartItem.user_id.equals(user_id)) {
            return res.status(403).json({ success: false, message: 'User not authorized to delete this cart item.' });
        }

        await cartItem.deleteOne();
        res.status(200).json({ success: true, message: 'Item removed from cart successfully.' });

    } catch (error) {
        console.error("Error removing item from cart:", error);
        res.status(500).json({ success: false, message: 'Server error while removing item from cart.' });
    }
});

// @desc    Clear user's cart
// @route   DELETE /api/cart/clear/:user_id (atau /api/cart/clear?user_id=xxx)
// @access  Public (Setelah middleware dihapus)
router.delete('/clear/:user_id', async (req, res) => {
    // Ambil user_id dari parameter rute.
    const { user_id } = req.params;
    // Alternatif: jika dari query string: const { user_id } = req.query; dan rute adalah DELETE /api/cart/clear

    if (!user_id) {
        return res.status(400).json({ success: false, message: 'User ID is required as a route parameter or query string.' });
    }
    if (!mongoose.Types.ObjectId.isValid(user_id)) {
        return res.status(400).json({ success: false, message: 'Invalid User ID format.' });
    }

    try {
        await Cart.deleteMany({ user_id });
        res.status(200).json({ success: true, message: 'Cart cleared successfully.' });
    } catch (error) {
        console.error("Error clearing cart:", error);
        res.status(500).json({ success: false, message: 'Server error while clearing cart.' });
    }
});

export default router;
