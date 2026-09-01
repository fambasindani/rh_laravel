// src/components/NotificationsDropdown.tsx
import React, { useState, useEffect } from 'react';
import { BellIcon } from '@heroicons/react/24/outline';
import Dropdown from './ui/Dropdown';
import { notificationsService } from '../services/notifications.service';
import type { Notification } from '../services/notifications.service';
import { useAuth } from '../hooks/useAuth';
import { format } from 'date-fns';
import { fr } from 'date-fns/locale';
import { useNavigate } from 'react-router-dom';

interface NotificationsDropdownProps {
  iconSize?: number;
  badgeSize?: number;
  dropdownWidth?: number;
}

const NotificationsDropdown: React.FC<NotificationsDropdownProps> = ({
  iconSize = 20,
  badgeSize = 16,
}) => {
  const [unreadNotifications, setUnreadNotifications] = useState<Notification[]>([]);
  const [loading, setLoading] = useState(false);
  const { user } = useAuth();
  const navigate = useNavigate();

  const fetchUnread = async () => {
    if (!user?.agentId) return;
    setLoading(true);
    try {
      const all = await notificationsService.getByAgent(user.agentId);
      const unread = all.filter((n) => !n.lu);
      setUnreadNotifications(unread);
    } catch (error) {
      console.error('Erreur chargement notifications:', error);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUnread();
    const interval = setInterval(fetchUnread, 30000);
    return () => clearInterval(interval);
  }, [user?.agentId]);

  const markAsRead = async (id: number) => {
    try {
      await notificationsService.markAsRead(id);
      setUnreadNotifications((prev) => prev.filter((n) => n.id !== id));
    } catch (error) {
      console.error('Erreur marquage lu:', error);
    }
  };

  const badgeContent = unreadNotifications.length > 9 ? '9+' : unreadNotifications.length;

  // Fonction pour tronquer le message à 18 caractères + "..."
  const truncateMessage = (msg: string) => {
    if (msg.length <= 18) return msg;
    return msg.substring(0, 23) + '...';
  };

  return (
    <Dropdown
      trigger={
        <button
          className="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 rounded-xl relative transition-colors"
          aria-label="Notifications"
        >
          <BellIcon style={{ width: iconSize, height: iconSize }} />
          {unreadNotifications.length > 0 && (
            <span
              className="absolute -top-0.5 -right-0.5 rounded-full bg-rose-500 text-white font-bold flex items-center justify-center ring-2 ring-white"
              style={{
                width: badgeSize,
                height: badgeSize,
                fontSize: badgeSize * 0.6,
              }}
            >
              {badgeContent}
            </span>
          )}
        </button>
      }
      align="right"
    >
      <div style={{ width: '200px', maxWidth: '200px', overflow: 'hidden' }}>
        {loading ? (
          <div className="px-4 py-2 text-sm text-gray-500">Chargement...</div>
        ) : unreadNotifications.length === 0 ? (
          <div className="px-4 py-2 text-sm text-gray-500">Aucune notification non lue</div>
        ) : (
          <div className="max-h-72 overflow-y-auto">
            {unreadNotifications.map((notif) => (
              <div
                key={notif.id}
                className="px-4 py-2 hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0"
                onClick={() => {
                  markAsRead(notif.id);
                  navigate('/notifications');
                }}
                title={notif.message} // Texte complet au survol
              >
                <p className="text-sm font-medium text-gray-800 truncate">
                  {truncateMessage(notif.message)}
                </p>
                <p className="text-xs text-gray-400 mt-0.5">
                  {format(new Date(notif.dateNotification), 'dd/MM/yyyy HH:mm', {
                    locale: fr,
                  })}
                </p>
              </div>
            ))}
            <div className="px-4 py-2 border-t border-slate-100">
              <button
                className="text-xs text-blue-600 hover:text-blue-800 font-medium"
                onClick={() => navigate('/notifications')}
              >
                Voir toutes les notifications
              </button>
            </div>
          </div>
        )}
      </div>
    </Dropdown>
  );
};

export default NotificationsDropdown;