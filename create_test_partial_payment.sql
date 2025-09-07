-- SQL to create a test partial payment
-- Run this in your database to create a payment with partial status

-- First, check existing payments
SELECT id, payment_reference, payment_status, total_amount, paid_amount, balance_amount 
FROM payments 
ORDER BY created_at DESC 
LIMIT 5;

-- Update the first payment to be partial (replace 1 with actual payment ID)
UPDATE payments 
SET 
    payment_status = 'partial',
    paid_amount = total_amount * 0.5,  -- Pay 50% of total
    balance_amount = total_amount * 0.5 -- 50% balance remaining
WHERE id = 1;  -- Replace with actual payment ID

-- Verify the update
SELECT id, payment_reference, payment_status, total_amount, paid_amount, balance_amount 
FROM payments 
WHERE id = 1;  -- Replace with actual payment ID