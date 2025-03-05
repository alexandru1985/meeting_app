FROM nginx:latest

# Remove default configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy custom configuration file
COPY nginx.conf /etc/nginx/conf.d/

WORKDIR /var/www

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
