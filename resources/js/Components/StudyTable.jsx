import React from 'react';

function StudyTable({ studies, onStudyClick }) {
    if (!studies || studies.length === 0) {
        return <p>No studies found with the current filters.</p>;
    }

    return (
        <div className="study-table-container">
            <h2>Study Titles</h2>
            <table className="study-table">
                <thead>
                    <tr>
                        <th>Title</th>
                    </tr>
                </thead>
                <tbody>
                    {studies.map(study => (
                        <tr key={study.id}>
                            <td>
                                <a
                                    href="#"
                                    onClick={(e) => {
                                        e.preventDefault();
                                        onStudyClick(study);
                                    }}
                                    className="study-title-link"
                                >
                                    {study.title}
                                </a>
                            </td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default StudyTable;
