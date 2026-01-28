# Assets Management System

A simple web-based assets management system built with Flask.

## Features

- User authentication with login/logout functionality
- Asset inventory dashboard
- Responsive design
- Session management

## Installation

1. Clone the repository:
```bash
git clone https://github.com/maneesh7787/assets-management-system.git
cd assets-management-system
```

2. Install dependencies:
```bash
pip install -r requirements.txt
```

## Usage

1. Run the application:
```bash
# For development with debug mode
FLASK_ENV=development FLASK_HOST=0.0.0.0 python app.py

# For production (recommended)
python app.py
```

2. Open your browser and navigate to:
```
http://localhost:5000
```

3. Login with the following credentials:

**⚠️ DEMO CREDENTIALS - FOR DEVELOPMENT/TESTING ONLY ⚠️**

**Admin User:**
- Username: `admin`
- Password: `admin123`

**Regular User:**
- Username: `user`
- Password: `user123`

**IMPORTANT:** These credentials are for demonstration purposes only. Never use these in production!

## Project Structure

```
assets-management-system/
├── app.py                 # Main application file
├── requirements.txt       # Python dependencies
├── templates/            # HTML templates
│   ├── base.html         # Base template
│   ├── login.html        # Login page
│   └── dashboard.html    # Dashboard page
└── README.md             # This file
```

## Security Notes

⚠️ **THIS IS A DEMO APPLICATION - NOT PRODUCTION READY** ⚠️

For production use, you MUST implement the following security measures:

- **Change the SECRET_KEY**: Set a secure, randomly generated `SECRET_KEY` environment variable
- **Use a database**: Store user credentials in a secure database, not in source code
- **Password hashing**: Already implemented with werkzeug.security (bcrypt-based)
- **Use HTTPS**: Always use HTTPS in production to encrypt data in transit
- **Implement CSRF protection**: Use Flask-WTF for CSRF tokens on forms
- **Add rate limiting**: Implement Flask-Limiter to prevent brute force attacks
- **Remove demo credentials**: Never expose credentials in README or source code
- **Session security**: Configure secure session cookies for production (HTTPS required)
- **Input validation**: Add comprehensive input validation and sanitization
- **Error handling**: Implement proper error pages without exposing sensitive information

## License

This project is open source and available under the MIT License.