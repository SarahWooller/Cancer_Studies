import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom'; // Import useParams and useNavigate
import { fetchStudyById } from '../services/api'; // <--- We'll create this new API function

function StudyDetail() {
    const { id } = useParams(); // Get the 'id' parameter from the URL
    const navigate = useNavigate(); // Hook for programmatic navigation

    const [study, setStudy] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const getStudyDetails = async () => {
            try {
                setLoading(true);
                setError(null);
                const data = await fetchStudyById(id); // Fetch data for the specific ID
                setStudy(data);
                setLoading(false);
            } catch (err) {
                console.error('Error fetching study details:', err);
                setError('Failed to load study details.');
                setLoading(false);
            }
        };

        if (id) {
            getStudyDetails();
        }
    }, [id]); // Re-run effect if ID changes (e.g., navigating from /studies/1 to /studies/2)

    if (loading) {
        return <div className="loading-message">Loading study details...</div>;
    }

    if (error) {
        return <div className="error-message">Error: {error}</div>;
    }

    if (!study) {
        return <div className="loading-message">Study not found.</div>; // Or redirect to 404
    }

    return (
        <div className="study-detail-panel">
            {/* Back button */}
            <button onClick={() => navigate(-1)}>Back to Studies</button> {/* navigate(-1) goes back */}

            <h2>{study.title}</h2>

            <h3>Keywords</h3>
            <p>
                {study.keywords && Array.isArray(study.keywords) && study.keywords.length > 0
                    ? study.keywords.map(keyword => keyword.keyword).join(', ')
                    : 'No keywords available.'}
            </p>

            <h3>Metadata</h3>
            {/* Display metadata. Assuming it's an object, you might want to stringify or format it. */}
            {study.metadata ? (
                <pre>{JSON.stringify(study.metadata, null, 2)}</pre>
            ) : (
                <p>No metadata available.</p>
            )}
        </div>
    );
}

export default StudyDetail;
