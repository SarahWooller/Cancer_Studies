// resources/js/components/NavBar.jsx

import React, { useState, useEffect, useRef } from 'react';

// Recursive component to handle nested dropdown items
function NestedDropdownItem({ keyword, selectedKeywords, onKeywordSelect, openPath, onItemClick, depth = 0, parentPath = [] }) {
    const isSelected = selectedKeywords.some(sk => sk.id === keyword.id);
    const hasChildren = keyword.children && keyword.children.length > 0;

    // Calculate currentItemPath here, so it's available for rendering children
    const currentItemPath = [...parentPath, keyword.id];

    // Determine if this specific item is part of the currently open path
    const isActiveInPath = openPath[depth] === keyword.id;

    // When clicking on the label (excluding the checkbox itself)
    const handleLabelClick = (event) => {
        // Prevent default label behavior (toggling checkbox)
        event.preventDefault();
        // Stop event propagation to prevent immediate closing from parent's click handler
        event.stopPropagation();

        onItemClick(keyword, currentItemPath);
    };

    // Determine if the nested dropdown should be shown
    const showNestedDropdown = hasChildren && isActiveInPath;

    return (
        <li className={`nested-dropdown-item depth-${depth} ${hasChildren ? 'has-children' : ''}`}>
            <div className="nested-dropdown-item-content-wrapper relative">
                <label
                    className={`checkbox-item flex items-center justify-between w-full ${isActiveInPath ? 'active-path-item' : ''}`}
                    onClick={handleLabelClick} // Handle click on label area
                >
                    <span className="flex items-center keyword-text-span"> {/* Added span for text styling */}
                        <input
                            type="checkbox"
                            checked={isSelected}
                            onChange={(e) => {
                                e.stopPropagation(); // Stop propagation to prevent label click from firing
                                onKeywordSelect(keyword);
                            }}
                            className="mr-2"
                        />
                        {keyword.keyword}
                    </span>
                    {hasChildren && (
                        <span className={`ml-2 text-gray-500 transition-transform duration-200 ${isActiveInPath ? 'rotate-90' : ''}`}>
                            &#9654; {/* Right arrow for nested dropdown indicator */}
                        </span>
                    )}
                </label>

                {/* Render nested dropdown only if active in path and has children */}
                {showNestedDropdown && (
                    <ul
                        className={`nested-dropdown-menu absolute bg-white border border-gray-200 rounded-md shadow-lg py-1 z-30`}
                        style={{
                            top: '0', // Align with the top of the parent li
                            left: 'calc(100% - 10px)', // Overlap parent's right edge by 10px
                            transform: 'translate(10px, 10px)', // Visual offset: 10px right, 10px down
                        }}
                    >
                        {keyword.children.map(child => (
                            <NestedDropdownItem
                                key={child.id}
                                keyword={child}
                                selectedKeywords={selectedKeywords}
                                onKeywordSelect={onKeywordSelect}
                                openPath={openPath}
                                onItemClick={onItemClick}
                                depth={depth + 1} // Increase depth for nested items
                                parentPath={currentItemPath} // Pass the path to this item down
                            />
                        ))}
                    </ul>
                )}
            </div>
        </li>
    );
}

// Main NavBar component
function NavBar({ keywords, selectedKeywords, onKeywordSelect }) {
    // State to manage the currently open path of keyword IDs
    // e.g., [topLevelId, childId, grandChildId]
    const [openPath, setOpenPath] = useState([]);
    const navbarRef = useRef(null); // Ref for the main navbar container for click-outside detection

    // Function to handle clicks on menu items (for opening/closing dropdowns)
    const handleItemClick = (clickedKeyword, currentItemPath) => {
        setOpenPath(prevOpenPath => {
            const clickedId = clickedKeyword.id;
            const clickedDepth = currentItemPath.length - 1; // Depth of the clicked item

            // If the clicked item is already the last item in the open path, close it
            if (prevOpenPath[clickedDepth] === clickedId && prevOpenPath.length === currentItemPath.length) {
                return prevOpenPath.slice(0, clickedDepth); // Close this level and any deeper
            }
            // If the clicked item is a sibling or a new path, update the path
            else {
                // If clicking a new item at the same or shallower depth, truncate and add
                if (clickedDepth < prevOpenPath.length) {
                    return [...prevOpenPath.slice(0, clickedDepth), clickedId];
                }
                // If clicking a deeper item, just extend the path
                return currentItemPath;
            }
        });
    };

    // Effect to handle clicks outside the Navbar to close all dropdowns
    useEffect(() => {
        const handleClickOutside = (event) => {
            // Check if the click target is outside the navbarRef's current element
            // AND ensure the click target is not a checkbox (as checkboxes have their own logic)
            if (navbarRef.current && !navbarRef.current.contains(event.target) && event.target.type !== 'checkbox') {
                setOpenPath([]); // Close all dropdowns
            }
        };
        document.addEventListener('mousedown', handleClickOutside);
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, []); // Empty dependency array means this runs once on mount and cleans up on unmount

    return (
        <nav className="navbar-container" ref={navbarRef}> {/* Attach ref here */}
            <ul className="horizontal-menu flex justify-center space-x-6">
                {keywords.map(topLevelKeyword => {
                    // Determine if this top-level item is part of the currently open path
                    const isActiveInPath = openPath[0] === topLevelKeyword.id;

                    return (
                        <li
                            key={topLevelKeyword.id}
                            className={`horizontal-menu-item relative ${isActiveInPath ? 'active-path-item' : ''}`}
                            onClick={(e) => {
                                // Prevent default label behavior if the click is on the label text
                                // This ensures only the checkbox itself toggles selection
                                if (e.target.tagName === 'SPAN' && e.target.closest('.checkbox-item')) {
                                    e.preventDefault();
                                }
                                // Only handle click for dropdown toggle if it's not the checkbox input itself
                                if (e.target.type !== 'checkbox') {
                                    e.stopPropagation(); // Prevent click from bubbling up and closing other menus
                                    handleItemClick(topLevelKeyword, [topLevelKeyword.id]);
                                }
                            }}
                        >
                            {/* Top-level keyword display */}
                            <label className="checkbox-item top-level-item flex items-center">
                                <input
                                    type="checkbox"
                                    checked={selectedKeywords.some(sk => sk.id === topLevelKeyword.id)}
                                    onChange={(e) => {
                                        e.stopPropagation(); // Stop propagation to prevent label click from firing
                                        onKeywordSelect(topLevelKeyword);
                                    }}
                                    className="mr-2"
                                />
                                <span className="font-semibold keyword-text-span cursor-pointer"> {/* Added span for text styling */}
                                    {topLevelKeyword.keyword}
                                </span>
                                {topLevelKeyword.children && topLevelKeyword.children.length > 0 && (
                                    <span className={`ml-1 mr-10 text-gray-500 text-xs transition-transform duration-200 ${isActiveInPath ? 'rotate-90' : ''}`}>
                                        &#9654; {/* Right arrow for consistency */}
                                    </span>
                                )}
                            </label>

                            {/* Dropdown menu for immediate children of top-level keyword */}
                            {topLevelKeyword.children && topLevelKeyword.children.length > 0 && isActiveInPath && (
                                <ul
                                    className="dropdown-menu absolute bg-white border border-gray-200 rounded-md shadow-lg py-1 z-20"
                                    style={{
                                        top: 'calc(100% - 10px)', // Overlap parent's bottom edge by 10px
                                        left: '0', // Align with the left edge of the parent horizontal menu item
                                        transform: 'translate(10px, 10px)', // Visual offset: 10px right, 10px down
                                    }}
                                >
                                    {topLevelKeyword.children.map(child => (
                                        <NestedDropdownItem
                                            key={child.id}
                                            keyword={child}
                                            selectedKeywords={selectedKeywords}
                                            onKeywordSelect={onKeywordSelect}
                                            openPath={openPath}
                                            onItemClick={handleItemClick} // Pass down the click handler
                                            depth={1} // Explicitly set depth to 1 for the first level of dropdowns
                                            parentPath={[topLevelKeyword.id]} // Pass the path to this parent
                                        />
                                    ))}
                                </ul>
                            )}
                        </li>
                    );
                })}
            </ul>
        </nav>
    );
}

export default NavBar;
