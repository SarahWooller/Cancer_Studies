import React, { useState, useEffect } from 'react';
import Header from './Header';
import NavBar from './NavBar';
import StudyTable from './StudyTable';
import StudyDetail from './StudyDetail';
import { fetchStudies, fetchKeywords } from '../services/api'; // We'll create this next

function App() {
    const [studies, setStudies] = useState([]);
    const [filteredStudies, setFilteredStudies] = useState([]);
    const [keywordsHierarchy, setKeywordsHierarchy] = useState([]);
    const [selectedKeywords, setSelectedKeywords] = useState([]); // Array of selected keyword objects
    const [selectedStudy, setSelectedStudy] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Initial data fetch: all studies and keywords hierarchy
    useEffect(() => {
        const loadInitialData = async () => {
            try {
                setLoading(true);
                const [studiesData, keywordsData] = await Promise.all([
                    fetchStudies(),
                    fetchKeywords()
                ]);
                setStudies(studiesData);
                setFilteredStudies(studiesData); // Initially, all studies are displayed
                setKeywordsHierarchy(keywordsData);
            } catch (err) {
                setError('Failed to load initial data: ' + err.message);
                console.error('Error loading initial data:', err);
            } finally {
                setLoading(false);
            }
        };
        loadInitialData();
    }, []);

    // Effect for filtering studies whenever selectedKeywords change
    useEffect(() => {
        const applyFilters = async () => {
            if (selectedKeywords.length === 0) {
                setFilteredStudies(studies); // If no keywords selected, show all
                return;
            }

            try {
                setLoading(true);
                // The API service needs to be updated to accept filters
                const filteredData = await fetchStudies(selectedKeywords);
                setFilteredStudies(filteredData);
            } catch (err) {
                setError('Failed to filter studies: ' + err.message);
                console.error('Error filtering studies:', err);
            } finally {
                setLoading(false);
            }
        };
        applyFilters();
    }, [selectedKeywords, studies]); // Re-run when selectedKeywords or original studies change

    const handleKeywordSelect = (keywords) => {
        // This function will receive an array of selected keyword objects
        setSelectedKeywords(keywords);
        setSelectedStudy(null); // Clear study detail when filters change
    };

    const handleStudyClick = (study) => {
        setSelectedStudy(study);
    };

    if (loading) {
        return <div className="loading-message">Loading data...</div>;
    }

    if (error) {
        return <div className="error-message">Error: {error}</div>;
    }

    return (
        <div className="app-container">
            <Header />
            <div className="main-content">
                <aside className="sidebar">
                    <NavBar
                        keywords={keywordsHierarchy}
                        onKeywordSelect={handleKeywordSelect}
                        selectedKeywords={selectedKeywords}
                    />
                </aside>
                <main className="content-area">
                    <StudyTable
                        studies={filteredStudies}
                        onStudyClick={handleStudyClick}
                    />
                    {selectedStudy && (
                        <StudyDetail study={selectedStudy} onClose={() => setSelectedStudy(null)} />
                    )}
                </main>
            </div>
        </div>
    );
}

export default App;
