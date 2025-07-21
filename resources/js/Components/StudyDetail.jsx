import React from 'react';

function StudyDetail({ study, onClose }) {
    if (!study) {
        return null; // Don't render if no study is selected
    }

    // Assuming study.metadata is already a JavaScript object/array (JSON parsed)
    // If it's a string, you might need to JSON.parse(study.metadata) here,
    // but Laravel's Eloquent casts should handle this if configured.
    const metadataToDisplay = JSON.stringify(study.metadata, null, 2); // Pretty print JSON

    return (
        <div className="study-detail-panel">
            <h2>Study Details: {study.title}</h2>
            <h3>Metadata:</h3>
            <pre>{metadataToDisplay}</pre>
            <button onClick={onClose}>Close Details</button>
        </div>
    );
}

export default StudyDetail;
