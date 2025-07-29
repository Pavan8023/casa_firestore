// Firebase configuration
const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Initialize Firebase services
const auth = firebase.auth();
const db = firebase.firestore();
const storage = firebase.storage();

// Authentication state observer
auth.onAuthStateChanged((user) => {
    if (user) {
        // User is signed in
        console.log('User is signed in:', user);
        // Update UI for signed-in user
        document.querySelectorAll('.auth-required').forEach(element => {
            element.style.display = 'block';
        });
        document.querySelectorAll('.auth-not-required').forEach(element => {
            element.style.display = 'none';
        });
    } else {
        // User is signed out
        console.log('User is signed out');
        // Update UI for signed-out user
        document.querySelectorAll('.auth-required').forEach(element => {
            element.style.display = 'none';
        });
        document.querySelectorAll('.auth-not-required').forEach(element => {
            element.style.display = 'block';
        });
    }
});

// Authentication functions
function signIn(email, password) {
    return auth.signInWithEmailAndPassword(email, password)
        .catch((error) => {
            console.error('Error signing in:', error);
            throw error;
        });
}

function signOut() {
    return auth.signOut()
        .catch((error) => {
            console.error('Error signing out:', error);
            throw error;
        });
}

function signUp(email, password) {
    return auth.createUserWithEmailAndPassword(email, password)
        .catch((error) => {
            console.error('Error signing up:', error);
            throw error;
        });
}

// Firestore functions
function addProject(projectData) {
    return db.collection('projects').add(projectData)
        .catch((error) => {
            console.error('Error adding project:', error);
            throw error;
        });
}

function getProjects() {
    return db.collection('projects').get()
        .catch((error) => {
            console.error('Error getting projects:', error);
            throw error;
        });
}

function addEvent(eventData) {
    return db.collection('events').add(eventData)
        .catch((error) => {
            console.error('Error adding event:', error);
            throw error;
        });
}

function getEvents() {
    return db.collection('events').get()
        .catch((error) => {
            console.error('Error getting events:', error);
            throw error;
        });
}

// Storage functions
function uploadFile(file, path) {
    const storageRef = storage.ref();
    const fileRef = storageRef.child(path);
    return fileRef.put(file)
        .catch((error) => {
            console.error('Error uploading file:', error);
            throw error;
        });
}

function getFileUrl(path) {
    const storageRef = storage.ref();
    const fileRef = storageRef.child(path);
    return fileRef.getDownloadURL()
        .catch((error) => {
            console.error('Error getting file URL:', error);
            throw error;
        });
} 