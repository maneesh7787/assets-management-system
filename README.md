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
python app.py
```

2. Open your browser and navigate to:
```
http://localhost:5000
```

3. Login with the following credentials:

**Admin User:**
- Username: `admin`
- Password: `admin123`

**Regular User:**
- Username: `user`
- Password: `user123`

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

- For production use, change the `SECRET_KEY` in `app.py`
- Store credentials securely (e.g., using environment variables or a database)
- Use HTTPS in production
- Implement proper password hashing (e.g., using bcrypt)

## License

This project is open source and available under the MIT License.