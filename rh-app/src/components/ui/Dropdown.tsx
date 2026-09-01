import React, { useState, useRef, useEffect } from 'react';

interface DropdownProps {
  trigger: React.ReactNode;
  children: React.ReactNode;
  align?: 'left' | 'right';
}

const Dropdown: React.FC<DropdownProps> = ({ trigger, children, align = 'left' }) => {
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <div className="relative inline-block text-left" ref={dropdownRef}>
      <div onClick={() => setIsOpen(!isOpen)} className="cursor-pointer">
        {trigger}
      </div>
      {isOpen && (
        <div
          className={`absolute z-10 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none ${
            align === 'right' ? 'right-0' : 'left-0'
          }`}
        >
          <div className="py-1">{children}</div>
        </div>
      )}
    </div>
  );
};

export const DropdownItem: React.FC<React.ButtonHTMLAttributes<HTMLButtonElement>> = ({
  children,
  className = '',
  ...props
}) => (
  <button
    className={`block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 ${className}`}
    {...props}
  >
    {children}
  </button>
);

export default Dropdown;