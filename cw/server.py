import os
from http.server import SimpleHTTPRequestHandler
from socketserver import TCPServer

# Define the directory where the static files are
os.chdir('cybersecurity-website')

# Set the port number you want the server to run on
PORT = 8000

Handler = SimpleHTTPRequestHandler

# Create the server
with TCPServer(("", PORT), Handler) as httpd:
    print(f"Serving at port {PORT}")
    httpd.serve_forever()
