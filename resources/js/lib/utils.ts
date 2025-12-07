import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * Date Utilities
 * These utilities handle dates consistently across the application,
 * avoiding timezone-related issues and ensuring proper formatting.
 */

/**
 * Parse a date string as a local date (not UTC) to avoid timezone shifts.
 * Handles various input formats: "YYYY-MM-DD", "DD/MM/YYYY", Date objects, etc.
 *
 * @param dateString - The date string to parse
 * @returns Date object in local timezone, or null if invalid
 */
export function parseLocalDate(dateString: string | null | undefined | Date): Date | null {
  if (!dateString) return null;

  // If already a Date object, return it
  if (dateString instanceof Date) {
    return isNaN(dateString.getTime()) ? null : dateString;
  }

  try {
    // Handle YYYY-MM-DD format (from database/API)
    const isoMatch = dateString.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (isoMatch) {
      const [, year, month, day] = isoMatch;
      // Create date in local timezone (not UTC)
      const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      return isNaN(date.getTime()) ? null : date;
    }

    // Handle DD/MM/YYYY format
    const ddmmyyyyMatch = dateString.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})/);
    if (ddmmyyyyMatch) {
      const [, day, month, year] = ddmmyyyyMatch;
      const date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
      return isNaN(date.getTime()) ? null : date;
    }

    // Fallback: try to parse with Date constructor
    // Note: This might have timezone issues for some formats
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? null : date;
  } catch (error) {
    console.error('Error parsing date:', error);
    return null;
  }
}

/**
 * Format a date for HTML input type="date" (YYYY-MM-DD format).
 * Ensures no timezone conversion occurs.
 *
 * @param dateString - The date string to format
 * @returns Formatted date string in YYYY-MM-DD format, or empty string if invalid
 */
export function formatDateForInput(dateString: string | null | undefined | Date): string {
  const date = parseLocalDate(dateString);
  if (!date) return '';

  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');

  return `${year}-${month}-${day}`;
}

/**
 * Format a date for display (DD/MM/YYYY format).
 * Consistent with Indian date format standards.
 *
 * @param dateString - The date string to format
 * @returns Formatted date string in DD/MM/YYYY format, or empty string if invalid
 */
export function formatDateForDisplay(dateString: string | null | undefined | Date): string {
  const date = parseLocalDate(dateString);
  if (!date) return '';

  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
}

/**
 * Calculate age from date of birth.
 *
 * @param dateOfBirth - The date of birth string
 * @returns Age in years, or null if invalid
 */
export function calculateAge(dateOfBirth: string | null | undefined | Date): number | null {
  const birthDate = parseLocalDate(dateOfBirth);
  if (!birthDate) return null;

  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const monthDiff = today.getMonth() - birthDate.getMonth();

  // Adjust age if birthday hasn't occurred this year
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }

  return age;
}
