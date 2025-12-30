# Archive Certificates Setup Guide

## S3 Configuration

The Archive Certificates feature requires AWS S3 for file storage. Follow these steps to configure S3:

### 1. Create AWS S3 Bucket

1. Log in to AWS Console
2. Navigate to S3 service
3. Create a new bucket (e.g., `your-church-name-certificates`)
4. Choose your preferred region (e.g., `us-east-1`)
5. Keep default settings for Block Public Access (recommended for security)

### 2. Create IAM User with S3 Access

1. Navigate to IAM service in AWS Console
2. Create a new IAM user (e.g., `salvation-admin-s3-user`)
3. Attach the following policy to grant S3 access:

```json
{
  "Version": "2012-10-17",
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:PutObject",
        "s3:GetObject",
        "s3:DeleteObject",
        "s3:ListBucket"
      ],
      "Resource": [
        "arn:aws:s3:::your-bucket-name/*",
        "arn:aws:s3:::your-bucket-name"
      ]
    }
  ]
}
```

4. Generate Access Keys for this user
5. Save the Access Key ID and Secret Access Key securely

### 3. Update .env File

Add the following configuration to your `.env` file:

```env
# AWS S3 Configuration for Archive Certificates
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=your_access_key_id_here
AWS_SECRET_ACCESS_KEY=your_secret_access_key_here
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=https://your-bucket-name.s3.us-east-1.amazonaws.com
```

**Important Notes:**
- Replace `your_access_key_id_here` with your actual AWS Access Key ID
- Replace `your_secret_access_key_here` with your actual AWS Secret Access Key
- Replace `your-bucket-name` with your actual S3 bucket name
- Adjust the region (`us-east-1`) if you created your bucket in a different region
- Adjust the AWS_URL to match your bucket name and region

### 4. Install AWS SDK (if not already installed)

Run the following command to ensure the AWS SDK is installed:

```bash
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies
```

### 5. Test S3 Connection

Test your S3 configuration using Laravel Tinker:

```bash
php artisan tinker
```

Then run:

```php
Storage::disk('s3')->put('test.txt', 'Hello World');
Storage::disk('s3')->exists('test.txt'); // Should return true
Storage::disk('s3')->get('test.txt'); // Should return 'Hello World'
Storage::disk('s3')->delete('test.txt');
```

If all commands work without errors, your S3 configuration is correct!

### 6. Clear Configuration Cache

After updating `.env`, clear the configuration cache:

```bash
php artisan config:clear
php artisan cache:clear
```

## File Storage Structure

Files will be organized in S3 as follows:

```
archive/
  birth/
    2020/
      1735574400_certificate1.pdf
      1735574401_certificate2.pdf
    2021/
      ...
  marriage/
    2020/
      ...
  death/
    2020/
      ...
```

## Security Considerations

1. **Never commit .env file** - Keep your AWS credentials secure
2. **Use IAM user with minimal permissions** - Only grant S3 access, nothing more
3. **Enable S3 bucket versioning** - Helps recover accidentally deleted files
4. **Consider enabling S3 encryption** - Encrypt data at rest
5. **Set up S3 lifecycle policies** - Automatically archive old files to Glacier for cost savings

## Troubleshooting

### Error: "Class 'League\Flysystem\AwsS3V3\AwsS3V3Adapter' not found"
**Solution:** Run `composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies`

### Error: "The AWS Access Key Id you provided does not exist in our records"
**Solution:** Verify your AWS_ACCESS_KEY_ID in `.env` is correct

### Error: "SignatureDoesNotMatch"
**Solution:** Verify your AWS_SECRET_ACCESS_KEY in `.env` is correct

### Error: "The bucket you are attempting to access must be addressed using the specified endpoint"
**Solution:** Ensure AWS_DEFAULT_REGION matches your bucket's region

### Files not uploading
**Solution:** Check IAM user permissions include `s3:PutObject` for your bucket

## Alternative: Using Local Storage (Development Only)

If you want to test without S3, you can use local storage temporarily:

1. In `.env`, set:
```env
FILESYSTEM_DISK=local
```

2. Files will be stored in `storage/app/private/archive/` instead

**Note:** This is NOT recommended for production. S3 provides better scalability, reliability, and backup options.

## Cost Estimates

AWS S3 pricing (as of 2025, subject to change):
- Storage: ~$0.023 per GB per month (Standard storage)
- PUT requests: ~$0.005 per 1,000 requests
- GET requests: ~$0.0004 per 1,000 requests

For 10,000 certificates (~10GB):
- Storage: ~$0.23/month
- Uploads: ~$0.05 one-time
- Downloads: Minimal (assuming moderate usage)

**Total estimated cost: Less than $5/month for typical church usage**

## Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode temporarily: `APP_DEBUG=true` in `.env`
3. Check AWS CloudTrail for S3 access logs
4. Verify IAM permissions in AWS Console
