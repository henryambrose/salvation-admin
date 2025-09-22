/**
 * Compress image files before upload to prevent 413 errors
 * Reduces file size while maintaining acceptable quality for gallery photos
 */

/**
 * Compress an image file
 * @param {File} file - The image file to compress
 * @param {Object} options - Compression options
 * @returns {Promise<File>} - Compressed image file
 */
export async function compressImage(file, options = {}) {
  const {
    maxWidth = 1920,
    maxHeight = 1080,
    quality = 0.8,
    maxSizeKB = 2048 // 2MB after compression
  } = options;

  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();

    img.onload = function() {
      // Calculate new dimensions maintaining aspect ratio
      let { width, height } = img;

      if (width > maxWidth) {
        height = (height * maxWidth) / width;
        width = maxWidth;
      }

      if (height > maxHeight) {
        width = (width * maxHeight) / height;
        height = maxHeight;
      }

      canvas.width = width;
      canvas.height = height;

      // Draw and compress
      ctx.drawImage(img, 0, 0, width, height);

      canvas.toBlob(
        (blob) => {
          if (blob) {
            // Check if compression achieved target size
            if (blob.size > maxSizeKB * 1024) {
              // Try with lower quality
              canvas.toBlob(
                (secondBlob) => {
                  const compressedFile = new File(
                    [secondBlob || blob],
                    file.name,
                    { type: file.type }
                  );
                  resolve(compressedFile);
                },
                file.type,
                quality * 0.7 // Lower quality for second attempt
              );
            } else {
              const compressedFile = new File([blob], file.name, { type: file.type });
              resolve(compressedFile);
            }
          } else {
            reject(new Error('Failed to compress image'));
          }
        },
        file.type,
        quality
      );
    };

    img.onerror = () => reject(new Error('Failed to load image'));
    img.src = URL.createObjectURL(file);
  });
}

/**
 * Compress multiple images
 * @param {FileList|File[]} files - Array of image files
 * @param {Object} options - Compression options
 * @returns {Promise<File[]>} - Array of compressed files
 */
export async function compressImages(files, options = {}) {
  const fileArray = Array.from(files);
  const compressedFiles = [];

  for (const file of fileArray) {
    if (file.type.startsWith('image/')) {
      try {
        const compressed = await compressImage(file, options);
        compressedFiles.push(compressed);
      } catch (error) {
        console.warn(`Failed to compress ${file.name}:`, error);
        compressedFiles.push(file); // Use original if compression fails
      }
    } else {
      compressedFiles.push(file); // Non-image files pass through
    }
  }

  return compressedFiles;
}

/**
 * Check if file needs compression
 * @param {File} file - File to check
 * @param {number} maxSize - Maximum size in bytes
 * @returns {boolean} - Whether file needs compression
 */
export function needsCompression(file, maxSize = 2 * 1024 * 1024) {
  return file.type.startsWith('image/') && file.size > maxSize;
}