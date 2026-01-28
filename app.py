from flask import Flask, render_template, request, redirect, url_for, session, flash
from functools import wraps
import os

app = Flask(__name__)
app.secret_key = os.environ.get('SECRET_KEY', 'dev-secret-key-change-in-production')

# Correct login credentials
USERS = {
    'admin': 'admin123',
    'user': 'user123'
}

# Sample assets database
ASSETS = [
    {'id': 1, 'name': 'Laptop Dell XPS', 'category': 'Electronics', 'status': 'Available'},
    {'id': 2, 'name': 'Office Chair', 'category': 'Furniture', 'status': 'In Use'},
    {'id': 3, 'name': 'Projector', 'category': 'Electronics', 'status': 'Available'},
]

def login_required(f):
    @wraps(f)
    def decorated_function(*args, **kwargs):
        if 'username' not in session:
            flash('Please log in to access this page.', 'warning')
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated_function

@app.route('/')
def index():
    if 'username' in session:
        return redirect(url_for('dashboard'))
    return redirect(url_for('login'))

@app.route('/login', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        username = request.form.get('username')
        password = request.form.get('password')
        
        if username in USERS and USERS[username] == password:
            session['username'] = username
            flash(f'Welcome {username}!', 'success')
            return redirect(url_for('dashboard'))
        else:
            flash('Invalid username or password. Please try again.', 'danger')
    
    return render_template('login.html')

@app.route('/dashboard')
@login_required
def dashboard():
    return render_template('dashboard.html', username=session['username'], assets=ASSETS)

@app.route('/logout')
def logout():
    session.pop('username', None)
    flash('You have been logged out.', 'info')
    return redirect(url_for('login'))

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
