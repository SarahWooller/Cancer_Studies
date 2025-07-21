import React, { useState } from 'react';
import '../../css/components/NavBar.css';

function NavBar({ keywords, onKeywordSelect, selectedKeywords }) {
    const [expandedKeywords, setExpandedKeywords] = useState([]);

    const handleKeywordClick = (keywordItem) => {
        // A keyword is "value" keyword if it has no children (it's a leaf node)
        const isValueKeyword = !(keywordItem.children && keywordItem.children.length > 0);

        if (isValueKeyword) {
            let newSelected = [];
            // IMPORTANT CHANGE: Compare by ID, not by keyword name
            const isSelected = selectedKeywords.some(sk => sk.id === keywordItem.id);

            if (isSelected) {
                // Deselect: filter out by ID
                newSelected = selectedKeywords.filter(sk => sk.id !== keywordItem.id);
            } else {
                // Select: add the full keyword object (which now has 'id')
                newSelected = [...selectedKeywords, keywordItem];
            }
            onKeywordSelect(newSelected); // Propagate selection change
        } else {
            // This is a category/subcategory, toggle its expanded state
            setExpandedKeywords(prevExpanded => {
                if (prevExpanded.includes(keywordItem.keyword)) {
                    return prevExpanded.filter(k => k !== keywordItem.keyword);
                } else {
                    return [...prevExpanded, keywordItem.keyword];
                }
            });
        }
    };

    const renderKeywordItem = (keyword) => {
        // IMPORTANT CHANGE: Check if selected by ID, not by keyword name
        const isSelected = selectedKeywords.some(sk => sk.id === keyword.id);
        const hasChildren = keyword.children && keyword.children.length > 0;
        const isExpanded = expandedKeywords.includes(keyword.keyword);

        return (
            <li key={keyword.id}> {/* Use keyword.id as key for list items */}
                {hasChildren ? (
                    <div
                        className="keyword-category-header"
                        onClick={() => handleKeywordClick(keyword)}
                    >
                        <span className="expand-icon">
                            {isExpanded ? '▼' : '▶'}
                        </span>
                        <span className="category-name">{keyword.keyword}</span>
                    </div>
                ) : (
                    <label className="checkbox-item">
                        <input
                            type="checkbox"
                            checked={isSelected}
                            onChange={() => handleKeywordClick(keyword)}
                        />
                        {keyword.keyword}
                    </label>
                )}
                {hasChildren && isExpanded && (
                    <ul className="keyword-children">
                        {keyword.children.map(child => renderKeywordItem(child))}
                    </ul>
                )}
            </li>
        );
    };

    return (
        <nav className="navbar-container">
            <h3>Keywords</h3>
            <ul>
                {keywords.map(topLevelKeyword => renderKeywordItem(topLevelKeyword))}
            </ul>
        </nav>
    );
}

export default NavBar;
